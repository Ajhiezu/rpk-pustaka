<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser as PdfParser;
use ZipArchive;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BookImportService
{
    protected PdfParser $pdfParser;

    public function __construct()
    {
        $this->pdfParser = new PdfParser();
    }

    /**
     * Process an uploaded digital book file (PDF or DOCX) and generate candidate metadata.
     */
    public function processDigitalFile(UploadedFile $file, string $batchId, ?string $renderedCoverBase64 = null, ?UploadedFile $manualCover = null, bool $skipPdfParse = false): array
    {
        @set_time_limit(180);
        @ini_set('memory_limit', '512M');

        $candidateId = 'cand_' . Str::random(12);
        $originalFilename = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();
        $fileHash = hash_file('sha256', $file->getRealPath());

        // 1. Store PDF/DOCX to private temporary storage
        $tempDir = 'temp/import/' . $batchId;
        $tempFilename = $candidateId . '.' . $extension;
        $tempFilePath = $tempDir . '/' . $tempFilename;
        Storage::disk('local')->putFileAs($tempDir, $file, $tempFilename);

        // 2. Process Cover (Client-side PDF.js rendered cover or manual upload)
        $coverPath = null;
        $coverWarning = null;

        if ($manualCover && $manualCover->isValid()) {
            $coverFilename = $candidateId . '_cover.' . $manualCover->getClientOriginalExtension();
            Storage::disk('public')->putFileAs($tempDir, $manualCover, $coverFilename);
            $coverPath = $tempDir . '/' . $coverFilename;
        } elseif (!empty($renderedCoverBase64) && str_starts_with($renderedCoverBase64, 'data:image')) {
            try {
                $coverData = explode(',', $renderedCoverBase64, 2);
                if (isset($coverData[1])) {
                    $decodedImage = base64_decode($coverData[1]);
                    if ($decodedImage !== false) {
                        $ext = str_contains($coverData[0], 'image/webp') ? 'webp' : 'jpg';
                        $coverFilename = $candidateId . '_cover.' . $ext;
                        Storage::disk('public')->put($tempDir . '/' . $coverFilename, $decodedImage);
                        $coverPath = $tempDir . '/' . $coverFilename;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Failed to save rendered cover for {$originalFilename}: " . $e->getMessage());
                $coverWarning = 'Gagal menyimpan sampul hasil rendering otomatis.';
            }
        }

        // 3. Extract Metadata
        $metadata = [
            'title' => $this->normalizeFilenameToTitle($originalFilename),
            'author' => null,
            'category_id' => null,
            'publisher' => null,
            'year' => null,
            'isbn' => null,
            'language' => 'Indonesia',
            'page_count' => null,
            'description' => null,
        ];

        $status = 'VALID';
        $statusMessages = [];

        if ($extension === 'pdf') {
            if (!$skipPdfParse) {
                $pdfExtraction = $this->extractPdfMetadata($file->getRealPath(), $originalFilename);
                $metadata['title'] = $pdfExtraction['title'] ?: $metadata['title'];
                $metadata['author'] = $pdfExtraction['author'];
                $metadata['year'] = $pdfExtraction['year'];
                $metadata['page_count'] = $pdfExtraction['page_count'];

                if (!empty($pdfExtraction['error'])) {
                    $status = 'WARNING';
                    $statusMessages[] = $pdfExtraction['error'];
                } elseif (!empty($pdfExtraction['warning'])) {
                    $status = 'WARNING';
                    $statusMessages[] = $pdfExtraction['warning'];
                }
            } else {
                $statusMessages[] = 'Ekstraksi PDF server dilewati (menggunakan nama berkas).';
            }
        } elseif ($extension === 'docx') {
            $docxExtraction = $this->extractDocxMetadata($file->getRealPath(), $originalFilename);
            $metadata['title'] = $docxExtraction['title'] ?: $metadata['title'];
            $metadata['author'] = $docxExtraction['author'];
            $metadata['year'] = $docxExtraction['year'];

            if (!empty($docxExtraction['warning'])) {
                $status = 'WARNING';
                $statusMessages[] = $docxExtraction['warning'];
            }
            if (!$coverPath) {
                $statusMessages[] = 'Cover otomatis belum tersedia untuk DOCX. Silakan unggah sampul manual.';
            }
        } else {
            $status = 'ERROR';
            $statusMessages[] = 'Format dokumen tidak didukung. Harap unggah file PDF atau DOCX.';
        }

        if ($coverWarning) {
            $statusMessages[] = $coverWarning;
        }

        // 4. Duplicate Check
        $duplicateCheck = $this->checkDuplicate($metadata['title'], $metadata['author'], $metadata['isbn']);
        if ($duplicateCheck['is_duplicate']) {
            $status = 'DUPLICATE';
            $statusMessages[] = $duplicateCheck['message'];
        }

        return [
            'id' => $candidateId,
            'original_filename' => $originalFilename,
            'file_path' => $tempFilePath,
            'file_hash' => $fileHash,
            'cover_path' => $coverPath,
            'collection_type' => 'digital',
            'title' => $metadata['title'],
            'author' => $metadata['author'],
            'category_id' => $metadata['category_id'],
            'location_id' => null,
            'stock' => 0,
            'available_stock' => 0,
            'publisher' => $metadata['publisher'],
            'year' => $metadata['year'],
            'isbn' => $metadata['isbn'],
            'language' => $metadata['language'] ?? 'Indonesia',
            'page_count' => $metadata['page_count'],
            'description' => $metadata['description'],
            'status' => $status,
            'status_messages' => array_unique($statusMessages),
        ];
    }

    /**
     * Process physical books spreadsheet (.xlsx, .xls, .csv).
     */
    public function processSpreadsheet(UploadedFile $file, string $batchId): array
    {
        $candidates = [];
        $categories = Category::all()->keyBy(fn($c) => strtolower(trim($c->name)));
        $locations = Location::all()->keyBy(fn($l) => strtolower(trim($l->name)));

        $spreadsheet = IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, true);

        if (empty($rows)) {
            return [];
        }

        // Header mapping
        $headerRow = array_shift($rows);
        $columnMap = $this->mapSpreadsheetColumns($headerRow);

        foreach ($rows as $rowIndex => $row) {
            // Skip empty rows
            $titleRaw = isset($columnMap['title']) ? trim((string)($row[$columnMap['title']] ?? '')) : '';
            if (empty($titleRaw)) {
                continue;
            }

            $candidateId = 'cand_xls_' . Str::random(10) . '_' . $rowIndex;
            $author = isset($columnMap['author']) ? trim((string)($row[$columnMap['author']] ?? '')) : null;
            $publisher = isset($columnMap['publisher']) ? trim((string)($row[$columnMap['publisher']] ?? '')) : null;
            $year = isset($columnMap['year']) ? (int)preg_replace('/[^0-9]/', '', (string)($row[$columnMap['year']] ?? '')) : null;
            $isbn = isset($columnMap['isbn']) ? trim((string)($row[$columnMap['isbn']] ?? '')) : null;
            $language = isset($columnMap['language']) && !empty($row[$columnMap['language']]) ? trim((string)$row[$columnMap['language']]) : 'Indonesia';
            $pageCount = isset($columnMap['page_count']) ? (int)preg_replace('/[^0-9]/', '', (string)($row[$columnMap['page_count']] ?? '')) : null;
            $description = isset($columnMap['description']) ? trim((string)($row[$columnMap['description']] ?? '')) : null;
            $stock = isset($columnMap['stock']) ? max(1, (int)preg_replace('/[^0-9]/', '', (string)($row[$columnMap['stock']] ?? '1'))) : 1;

            // Category matching (optional)
            $categoryNameRaw = isset($columnMap['category']) ? trim((string)($row[$columnMap['category']] ?? '')) : '';
            $categoryId = null;
            $statusMessages = [];
            $status = 'VALID';

            if (!empty($categoryNameRaw)) {
                $matchedCat = $categories->get(strtolower($categoryNameRaw));
                if ($matchedCat) {
                    $categoryId = $matchedCat->id;
                }
            }

            // Location matching (optional)
            $locationNameRaw = isset($columnMap['location']) ? trim((string)($row[$columnMap['location']] ?? '')) : '';
            $locationId = null;
            if (!empty($locationNameRaw)) {
                $matchedLoc = $locations->get(strtolower($locationNameRaw));
                if ($matchedLoc) {
                    $locationId = $matchedLoc->id;
                }
            }

            // Author (optional fallback)
            if (empty($author)) {
                $author = 'Tanpa Penulis';
            }

            // Year normalization
            if ($year && ($year < 1800 || $year > (date('Y') + 1))) {
                $year = null;
            }

            // Duplicate detection
            $duplicateCheck = $this->checkDuplicate($titleRaw, $author, $isbn);
            if ($duplicateCheck['is_duplicate']) {
                $status = 'DUPLICATE';
                $statusMessages[] = $duplicateCheck['message'];
            }

            $candidates[] = [
                'id' => $candidateId,
                'original_filename' => 'Spreadsheet Row ' . $rowIndex,
                'file_path' => null,
                'file_hash' => null,
                'cover_path' => null,
                'collection_type' => 'fisik',
                'title' => $titleRaw,
                'author' => $author ?: null,
                'category_id' => $categoryId,
                'location_id' => $locationId,
                'stock' => $stock,
                'available_stock' => $stock,
                'publisher' => $publisher ?: null,
                'year' => $year ?: null,
                'isbn' => $isbn ?: null,
                'language' => $language,
                'page_count' => $pageCount ?: null,
                'description' => $description ?: null,
                'status' => $status,
                'status_messages' => array_unique($statusMessages),
            ];
        }

        return $candidates;
    }

    /**
     * Map spreadsheet header columns to system fields.
     */
    protected function mapSpreadsheetColumns(array $headerRow): array
    {
        $map = [];
        foreach ($headerRow as $colKey => $colName) {
            $normalized = strtolower(trim((string)$colName));
            if (in_array($normalized, ['judul', 'title', 'judul buku', 'book title', 'nama buku'])) {
                $map['title'] = $colKey;
            } elseif (in_array($normalized, ['penulis', 'author', 'pengarang', 'penulis buku', 'creator'])) {
                $map['author'] = $colKey;
            } elseif (in_array($normalized, ['kategori', 'category', 'klasifikasi', 'genre'])) {
                $map['category'] = $colKey;
            } elseif (in_array($normalized, ['lokasi', 'location', 'rak', 'posisi rak', 'lokasi rak'])) {
                $map['location'] = $colKey;
            } elseif (in_array($normalized, ['penerbit', 'publisher'])) {
                $map['publisher'] = $colKey;
            } elseif (in_array($normalized, ['tahun', 'year', 'tahun terbit', 'publication year'])) {
                $map['year'] = $colKey;
            } elseif (in_array($normalized, ['isbn', 'isbn10', 'isbn13', 'no isbn', 'nomor isbn'])) {
                $map['isbn'] = $colKey;
            } elseif (in_array($normalized, ['bahasa', 'language', 'lang'])) {
                $map['language'] = $colKey;
            } elseif (in_array($normalized, ['halaman', 'page count', 'pages', 'jumlah halaman', 'total halaman'])) {
                $map['page_count'] = $colKey;
            } elseif (in_array($normalized, ['stok', 'stock', 'jumlah', 'qty', 'eksemplar', 'total stok'])) {
                $map['stock'] = $colKey;
            } elseif (in_array($normalized, ['deskripsi', 'description', 'sinopsis', 'keterangan'])) {
                $map['description'] = $colKey;
            }
        }
        return $map;
    }

    /**
     * Extract metadata from PDF with ultra-fast stream reading and zero heavy parsing overhead.
     */
    protected function extractPdfMetadata(string $realPath, string $originalFilename): array
    {
        $result = [
            'title' => null,
            'author' => null,
            'year' => null,
            'page_count' => null,
            'error' => null,
            'warning' => null,
        ];

        // 1. Fast Stream-Based PDF Scanner (<1ms execution time)
        $fastResult = $this->extractPdfMetadataFast($realPath);
        if (!empty($fastResult['title'])) {
            $result['title'] = $fastResult['title'];
        }
        if (!empty($fastResult['author'])) {
            $result['author'] = $fastResult['author'];
        }
        if (!empty($fastResult['year'])) {
            $result['year'] = $fastResult['year'];
        }
        if (!empty($fastResult['page_count'])) {
            $result['page_count'] = $fastResult['page_count'];
        }

        // 2. If fast scanner found a title, return immediately without heavy Smalot parsing
        if (!empty($result['title']) && !empty($result['page_count'])) {
            return $result;
        }

        // 3. Optional fallback to Smalot only if title is missing and memory permit
        if (empty($result['title']) && memory_get_usage() < 128 * 1024 * 1024) {
            try {
                $parser = new PdfParser();
                $pdf = $parser->parseFile($realPath);
                $details = $pdf->getDetails();

                if (empty($result['title']) && !empty($details['Title']) && is_string($details['Title']) && strlen(trim($details['Title'])) > 2) {
                    $cleanedTitle = trim($details['Title']);
                    if (!preg_match('/^(microsoft word|untitled|wps office|canva|adobe|print)/i', $cleanedTitle)) {
                        $result['title'] = $cleanedTitle;
                    }
                }

                if (empty($result['author']) && !empty($details['Author']) && is_string($details['Author']) && strlen(trim($details['Author'])) > 1) {
                    $cleanedAuthor = trim($details['Author']);
                    if (!preg_match('/^(administrator|user|admin|microsoft|wps|canva)/i', $cleanedAuthor)) {
                        $result['author'] = $cleanedAuthor;
                    }
                }

                if (empty($result['page_count'])) {
                    try {
                        $pages = $pdf->getPages();
                        $result['page_count'] = count($pages);
                    } catch (\Throwable $e) {}
                }
            } catch (\Throwable $e) {
                // Ignore Smalot errors silently - filename title will be used as ultimate fallback
            }
        }

        return $result;
    }

    /**
     * Fast binary stream reader for PDF metadata (/Title, /Author, /CreationDate, /Count).
     */
    protected function extractPdfMetadataFast(string $realPath): array
    {
        $result = [
            'title' => null,
            'author' => null,
            'year' => null,
            'page_count' => null,
        ];

        try {
            $fileSize = filesize($realPath);
            if ($fileSize === 0) {
                return $result;
            }

            // Read first 128KB and last 64KB where metadata dictionaries reside
            $fp = @fopen($realPath, 'rb');
            if (!$fp) {
                return $result;
            }

            $header = fread($fp, 131072);
            $footer = '';
            if ($fileSize > 131072) {
                fseek($fp, max(0, $fileSize - 65536));
                $footer = fread($fp, 65536);
            }
            fclose($fp);

            $content = $header . "\n" . $footer;

            // 1. Page Count (/Type /Pages ... /Count N)
            if (preg_match('/\/Type\s*\/Pages[^\/>]*\/Count\s+(\d+)/i', $content, $m) ||
                preg_match('/\/Count\s+(\d+)\s*\/Type\s*\/Pages/i', $content, $m) ||
                preg_match('/\/Count\s+(\d+)/i', $content, $m)) {
                $count = (int)$m[1];
                if ($count > 0 && $count < 20000) {
                    $result['page_count'] = $count;
                }
            }

            // 2. Title (/Title (...))
            if (preg_match('/\/Title\s*\(([^)]+)\)/i', $content, $m)) {
                $title = $this->decodePdfString(trim($m[1]));
                if ($title && strlen($title) > 2 && !preg_match('/^(microsoft word|untitled|wps office|canva|adobe|print)/i', $title)) {
                    $result['title'] = $title;
                }
            } elseif (preg_match('/\/Title\s*<([0-9a-fA-F]+)>/i', $content, $m)) {
                $title = $this->decodePdfHexString($m[1]);
                if ($title && strlen($title) > 2 && !preg_match('/^(microsoft word|untitled|wps office|canva|adobe|print)/i', $title)) {
                    $result['title'] = $title;
                }
            }

            // 3. Author (/Author (...))
            if (preg_match('/\/Author\s*\(([^)]+)\)/i', $content, $m)) {
                $author = $this->decodePdfString(trim($m[1]));
                if ($author && strlen($author) > 1 && !preg_match('/^(administrator|user|admin|microsoft|wps|canva)/i', $author)) {
                    $result['author'] = $author;
                }
            } elseif (preg_match('/\/Author\s*<([0-9a-fA-F]+)>/i', $content, $m)) {
                $author = $this->decodePdfHexString($m[1]);
                if ($author && strlen($author) > 1 && !preg_match('/^(administrator|user|admin|microsoft|wps|canva)/i', $author)) {
                    $result['author'] = $author;
                }
            }

            // 4. CreationDate (/CreationDate (D:YYYY...))
            if (preg_match('/\/CreationDate\s*\(\s*D?:?(\d{4})/i', $content, $m)) {
                $year = (int)$m[1];
                if ($year >= 1800 && $year <= ((int)date('Y') + 1)) {
                    $result['year'] = $year;
                }
            }
        } catch (\Throwable $e) {
            // Ignore fast scan errors gracefully
        }

        return $result;
    }

    /**
     * Decode literal PDF string escaping and UTF-16BE encoding.
     */
    protected function decodePdfString(string $str): ?string
    {
        $str = str_replace(['\\(', '\\)', '\\\\'], ['(', ')', '\\'], $str);
        if (str_starts_with($str, "\xFE\xFF")) {
            return @mb_convert_encoding(substr($str, 2), 'UTF-8', 'UTF-16BE') ?: null;
        }
        $str = trim($str);
        return mb_check_encoding($str, 'UTF-8') ? $str : @utf8_encode($str);
    }

    /**
     * Decode hex-encoded PDF string (<FEFF...>).
     */
    protected function decodePdfHexString(string $hex): ?string
    {
        $bin = @hex2bin($hex);
        if (!$bin) return null;
        if (str_starts_with($bin, "\xFE\xFF")) {
            return @mb_convert_encoding(substr($bin, 2), 'UTF-8', 'UTF-16BE') ?: null;
        }
        $bin = trim($bin);
        return mb_check_encoding($bin, 'UTF-8') ? $bin : @utf8_encode($bin);
    }

    /**
     * Extract metadata from DOCX using native ZipArchive and core.xml.
     */
    protected function extractDocxMetadata(string $realPath, string $originalFilename): array
    {
        $result = [
            'title' => null,
            'author' => null,
            'year' => null,
            'warning' => null,
        ];

        $zip = new ZipArchive();
        if ($zip->open($realPath) === true) {
            $coreXml = $zip->getFromName('docProps/core.xml');
            $zip->close();

            if ($coreXml !== false) {
                try {
                    $xml = simplexml_load_string($coreXml);
                    if ($xml) {
                        $namespaces = $xml->getNamespaces(true);
                        $dc = $xml->children($namespaces['dc'] ?? 'http://purl.org/dc/elements/1.1/');
                        $dcterms = $xml->children($namespaces['dcterms'] ?? 'http://purl.org/dc/terms/');

                        if (isset($dc->title) && strlen(trim((string)$dc->title)) > 2) {
                            $result['title'] = trim((string)$dc->title);
                        }

                        if (isset($dc->creator) && strlen(trim((string)$dc->creator)) > 1) {
                            $result['author'] = trim((string)$dc->creator);
                        }

                        if (isset($dcterms->created)) {
                            $createdDate = (string)$dcterms->created;
                            if (preg_match('/(\d{4})/', $createdDate, $matches)) {
                                $year = (int)$matches[1];
                                if ($year >= 1800 && $year <= (date('Y') + 1)) {
                                    $result['year'] = $year;
                                }
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    $result['warning'] = 'Gagal memproses XML metadata DOCX: ' . $e->getMessage();
                }
            }
        } else {
            $result['warning'] = 'Gagal membuka struktur arsip DOCX.';
        }

        return $result;
    }

    /**
     * Normalize filename into readable candidate book title.
     */
    public function normalizeFilenameToTitle(string $filename): string
    {
        // Strip extension
        $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);

        // Normalize separators (_, -, .) to space
        $normalized = preg_replace('/[_\-\.\+]+/', ' ', $nameWithoutExt);

        // Remove redundant multiple spaces
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        // Capitalize words cleanly
        return trim(ucwords(strtolower(trim($normalized))));
    }

    /**
     * Check if a candidate matches existing books in database.
     */
    public function checkDuplicate(?string $title, ?string $author, ?string $isbn): array
    {
        // Check 1: ISBN Match
        if (!empty($isbn)) {
            $cleanIsbn = preg_replace('/[^0-9Xx]/', '', $isbn);
            if (!empty($cleanIsbn)) {
                $existingByIsbn = Book::where('isbn', $isbn)->orWhere('isbn', $cleanIsbn)->first();
                if ($existingByIsbn) {
                    return [
                        'is_duplicate' => true,
                        'message' => "Duplikat ISBN: Nomor ISBN telah digunakan oleh naskah \"{$existingByIsbn->title}\".",
                    ];
                }
            }
        }

        // Check 2: Exact Title Match
        if (!empty($title)) {
            $cleanTitle = trim(strtolower($title));
            $existingTitle = Book::whereRaw('LOWER(TRIM(title)) = ?', [$cleanTitle])->first();
            if ($existingTitle) {
                return [
                    'is_duplicate' => true,
                    'message' => "Kemungkinan Duplikat: Judul \"{$existingTitle->title}\" sudah terdaftar di katalog pustaka.",
                ];
            }
        }

        return [
            'is_duplicate' => false,
            'message' => null,
        ];
    }

    /**
     * Execute final batch import with per-candidate transaction and partial success handling.
     */
    public function executeFinalImport(array $candidates, array $selectedIds = []): array
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $total = count($candidates);
        $imported = 0;
        $duplicateCount = 0;
        $failedCount = 0;

        $results = [
            'success' => [],
            'duplicate' => [],
            'failed' => [],
        ];

        foreach ($candidates as $cand) {
            $candId = $cand['id'] ?? '';
            // If specific IDs selected, filter
            if (!empty($selectedIds) && !in_array($candId, $selectedIds)) {
                continue;
            }

            $title = trim($cand['title'] ?? '');
            $author = trim($cand['author'] ?? '');
            $categoryId = $cand['category_id'] ?? null;
            $collectionType = $cand['collection_type'] ?? 'digital';
            $locationId = $cand['location_id'] ?? null;
            $stock = (int)($cand['stock'] ?? 0);
            $tempFilePath = $cand['file_path'] ?? null;
            $tempCoverPath = $cand['cover_path'] ?? null;

            // 1. Validation & Auto-Fallbacks
            $errors = [];
            if (empty($title)) {
                $errors[] = 'Judul buku wajib diisi.';
            }

            // Auto-fallback for author if not provided
            if (empty($author)) {
                $author = 'Tanpa Penulis';
            }

            // Auto-fallback for category to 'Umum' if not selected
            if (empty($categoryId) || !Category::where('id', $categoryId)->exists()) {
                $defaultCat = Category::firstOrCreate(['name' => 'Umum'], ['slug' => 'umum']);
                $categoryId = $defaultCat->id;
            }

            if ($collectionType === 'digital') {
                if (empty($tempFilePath) || !Storage::disk('local')->exists($tempFilePath)) {
                    $errors[] = 'Berkas naskah digital PDF tidak ditemukan di penyimpanan sementara.';
                }
            } elseif ($collectionType === 'fisik') {
                if ($stock < 1) {
                    $stock = 1;
                }
                // Location is optional for physical books
            }

            if (!empty($errors)) {
                $failedCount++;
                $results['failed'][] = [
                    'id' => $candId,
                    'title' => $title ?: 'Tanpa Judul',
                    'reason' => implode(', ', $errors),
                ];
                continue;
            }

            // 2. Duplicate Check
            $dupCheck = $this->checkDuplicate($title, $author, $cand['isbn'] ?? null);
            if ($dupCheck['is_duplicate']) {
                $duplicateCount++;
                $results['duplicate'][] = [
                    'id' => $candId,
                    'title' => $title,
                    'reason' => $dupCheck['message'],
                ];
                continue;
            }

            // 3. Execute Transaction per Candidate
            $movedPdfPath = null;
            $movedCoverPath = null;

            DB::beginTransaction();
            try {
                // Generate Unique Book Code (Reusing existing RPK project standard)
                $bookCode = $this->generateUniqueBookCode();

                // Generate Unique Slug (Reusing existing RPK project standard)
                $slug = Str::slug($title) . '-' . uniqid();

                // Move temporary PDF to permanent private storage
                $finalPdfPath = null;
                if ($collectionType === 'digital' && !empty($tempFilePath) && Storage::disk('local')->exists($tempFilePath)) {
                    $finalPdfName = 'pdf_' . uniqid() . '_' . basename($tempFilePath);
                    $finalPdfPath = 'books/pdf/' . $finalPdfName;
                    Storage::disk('local')->move($tempFilePath, $finalPdfPath);
                    $movedPdfPath = $finalPdfPath;
                }

                // Move temporary Cover to permanent public storage
                $finalCoverPath = null;
                if (!empty($tempCoverPath) && Storage::disk('public')->exists($tempCoverPath)) {
                    $finalCoverName = 'cover_' . uniqid() . '_' . basename($tempCoverPath);
                    $finalCoverPath = 'books/' . $finalCoverName;
                    Storage::disk('public')->move($tempCoverPath, $finalCoverPath);
                    $movedCoverPath = $finalCoverPath;
                }

                // Create Book Record with properly sanitized NULL values for unique/nullable columns
                $cleanIsbn = !empty($cand['isbn']) && trim($cand['isbn']) !== '' ? trim($cand['isbn']) : null;
                $cleanPublisher = !empty($cand['publisher']) && trim($cand['publisher']) !== '' ? trim($cand['publisher']) : null;
                $cleanDescription = !empty($cand['description']) && trim($cand['description']) !== '' ? trim($cand['description']) : null;
                $cleanYear = !empty($cand['year']) ? (int)$cand['year'] : null;
                $cleanPageCount = !empty($cand['page_count']) ? (int)$cand['page_count'] : null;

                $book = Book::create([
                    'book_code' => $bookCode,
                    'title' => $title,
                    'slug' => $slug,
                    'author' => $author,
                    'category_id' => $categoryId,
                    'location_id' => $collectionType === 'digital' ? null : $locationId,
                    'publisher' => $cleanPublisher,
                    'year' => $cleanYear,
                    'isbn' => $cleanIsbn,
                    'language' => !empty($cand['language']) ? trim($cand['language']) : 'Indonesia',
                    'page_count' => $cleanPageCount,
                    'collection_type' => $collectionType,
                    'stock' => $collectionType === 'digital' ? 0 : $stock,
                    'available_stock' => $collectionType === 'digital' ? 0 : $stock,
                    'price' => 0,
                    'fine_type' => 'fixed',
                    'fine_value' => '50000',
                    'description' => $cleanDescription,
                    'image' => $finalCoverPath,
                    'pdf_path' => $finalPdfPath,
                ]);

                DB::commit();

                $imported++;
                $results['success'][] = [
                    'id' => $candId,
                    'book_id' => $book->id,
                    'book_code' => $bookCode,
                    'title' => $title,
                ];
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error("Bulk import error on candidate {$title}: " . $e->getMessage());

                // Rollback moved files if any
                if ($movedPdfPath && Storage::disk('local')->exists($movedPdfPath)) {
                    Storage::disk('local')->delete($movedPdfPath);
                }
                if ($movedCoverPath && Storage::disk('public')->exists($movedCoverPath)) {
                    Storage::disk('public')->delete($movedCoverPath);
                }

                $failedCount++;
                $results['failed'][] = [
                    'id' => $candId,
                    'title' => $title,
                    'reason' => 'Database error: ' . $e->getMessage(),
                ];
            }
        }

        return [
            'total' => $total,
            'imported' => $imported,
            'duplicate' => $duplicateCount,
            'failed' => $failedCount,
            'results' => $results,
        ];
    }

    protected array $allocatedCodes = [];

    /**
     * Generate unique book code following RPK standard (RPK-B0001).
     */
    public function generateUniqueBookCode(): string
    {
        $lastId = Book::max('id') ?? 0;
        $number = $lastId + 1;
        do {
            $code = 'RPK-B' . str_pad($number, 4, '0', STR_PAD_LEFT);
            $exists = in_array($code, $this->allocatedCodes) || Book::where('book_code', $code)->exists();
            $number++;
        } while ($exists);

        $this->allocatedCodes[] = $code;
        return $code;
    }

    /**
     * Delete temporary candidate files.
     */
    public function removeCandidateFiles(array $candidate): void
    {
        if (!empty($candidate['file_path']) && Storage::disk('local')->exists($candidate['file_path'])) {
            Storage::disk('local')->delete($candidate['file_path']);
        }
        if (!empty($candidate['cover_path']) && Storage::disk('public')->exists($candidate['cover_path'])) {
            Storage::disk('public')->delete($candidate['cover_path']);
        }
    }

    /**
     * Save candidate data to an isolated candidate JSON file on disk to prevent session race conditions.
     */
    public function saveCandidate(string $batchId, array $candidate): void
    {
        $dir = 'temp/import/' . $batchId;
        if (!Storage::disk('local')->exists($dir)) {
            Storage::disk('local')->makeDirectory($dir);
        }
        $path = $dir . '/' . $candidate['id'] . '.json';
        Storage::disk('local')->put($path, json_encode($candidate, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Get all candidates for a batch from disk JSON files with session fallback.
     */
    public function getBatchCandidates(string $batchId): array
    {
        $dir = 'temp/import/' . $batchId;
        $candidates = [];

        if (Storage::disk('local')->exists($dir)) {
            $files = Storage::disk('local')->files($dir);
            foreach ($files as $file) {
                if (str_ends_with($file, '.json')) {
                    $content = Storage::disk('local')->get($file);
                    $cand = json_decode($content, true);
                    if ($cand && isset($cand['id'])) {
                        $candidates[$cand['id']] = $cand;
                    }
                }
            }
        }

        // Fallback to session if empty
        if (empty($candidates)) {
            $sessionKey = 'import_batch_' . $batchId;
            $batch = session($sessionKey);
            if ($batch && !empty($batch['candidates'])) {
                $candidates = $batch['candidates'];
            }
        }

        return $candidates;
    }

    /**
     * Delete candidate JSON file and its associated temporary files.
     */
    public function removeCandidateFromBatch(string $batchId, string $candidateId): void
    {
        $candidates = $this->getBatchCandidates($batchId);
        if (isset($candidates[$candidateId])) {
            $cand = $candidates[$candidateId];
            $this->removeCandidateFiles($cand);
            $jsonPath = 'temp/import/' . $batchId . '/' . $candidateId . '.json';
            if (Storage::disk('local')->exists($jsonPath)) {
                Storage::disk('local')->delete($jsonPath);
            }
        }
    }

    /**
     * Clean up all temporary files of a specific batch.
     */
    public function cleanupBatch(string $batchId): void
    {
        $localDir = 'temp/import/' . $batchId;
        if (Storage::disk('local')->exists($localDir)) {
            Storage::disk('local')->deleteDirectory($localDir);
        }
        if (Storage::disk('public')->exists($localDir)) {
            Storage::disk('public')->deleteDirectory($localDir);
        }
    }

    /**
     * Cleanup abandoned batches older than given hours.
     */
    public function cleanupAbandonedBatches(int $hours = 24): void
    {
        $now = time();
        $cutoff = $now - ($hours * 3600);

        foreach (['local', 'public'] as $diskName) {
            $disk = Storage::disk($diskName);
            $directories = $disk->directories('temp/import');
            foreach ($directories as $dir) {
                $lastModified = $disk->lastModified($dir);
                if ($lastModified < $cutoff) {
                    $disk->deleteDirectory($dir);
                }
            }
        }
    }
}
