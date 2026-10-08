<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Location;
use App\Services\BookImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class BookImportController extends Controller
{
    protected BookImportService $importService;

    public function __construct(BookImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Display the Bulk Import upload page.
     */
    public function create(): View
    {
        $categories = Category::all();
        $locations = Location::all();
        $batchId = Str::uuid()->toString();

        return view('admin.books.import', compact('categories', 'locations', 'batchId'));
    }

    /**
     * Process digital document uploads (AJAX batch endpoint).
     */
    public function uploadDigital(Request $request): JsonResponse
    {
        @set_time_limit(180);
        @ini_set('memory_limit', '512M');

        $request->validate([
            'batch_id' => 'required|string',
            'files' => 'required|array',
            'files.*' => 'required|file|max:51200', // max 50MB per file
            'rendered_covers' => 'nullable|array',
            'skip_pdf_parse' => 'nullable|boolean',
        ]);

        $batchId = preg_replace('/[^a-zA-Z0-9_\-]/', '', $request->input('batch_id'));
        $uploadedFiles = $request->file('files', []);
        $renderedCoversInput = $request->input('rendered_covers', []);
        $renderedCoversFiles = $request->file('rendered_covers', []);
        $skipPdfParse = $request->boolean('skip_pdf_parse', false);

        $newCandidates = [];

        foreach ($uploadedFiles as $index => $file) {
            try {
                $coverInput = $renderedCoversInput[$index] ?? null;
                $coverBase64 = is_string($coverInput) ? $coverInput : null;
                $manualCoverFile = $renderedCoversFiles[$index] ?? null;
                if (!($manualCoverFile instanceof \Illuminate\Http\UploadedFile) || !$manualCoverFile->isValid()) {
                    $manualCoverFile = null;
                }

                $candidate = $this->importService->processDigitalFile($file, $batchId, $coverBase64, $manualCoverFile, $skipPdfParse);
                $this->importService->saveCandidate($batchId, $candidate);
                $newCandidates[] = $candidate;
            } catch (\Throwable $e) {
                Log::error("Error processing digital file {$file->getClientOriginalName()}: " . $e->getMessage());
                
                $title = $this->importService->normalizeFilenameToTitle($file->getClientOriginalName());
                $candidate = [
                    'id' => 'cand_' . Str::random(12),
                    'original_filename' => $file->getClientOriginalName(),
                    'file_path' => null,
                    'file_hash' => null,
                    'cover_path' => null,
                    'collection_type' => 'digital',
                    'title' => $title,
                    'author' => null,
                    'category_id' => null,
                    'location_id' => null,
                    'stock' => 0,
                    'available_stock' => 0,
                    'publisher' => null,
                    'year' => null,
                    'isbn' => null,
                    'language' => 'Indonesia',
                    'page_count' => null,
                    'description' => null,
                    'status' => 'WARNING',
                    'status_messages' => ['Gagal ekstraksi metadata otomatis: ' . Str::limit($e->getMessage(), 100)],
                ];
                $this->importService->saveCandidate($batchId, $candidate);
                $newCandidates[] = $candidate;
            }
        }

        $allCandidates = $this->importService->getBatchCandidates($batchId);

        return response()->json([
            'success' => true,
            'batch_id' => $batchId,
            'total_candidates' => count($allCandidates),
            'processed' => $newCandidates,
        ]);
    }

    /**
     * Process physical books spreadsheet upload (.xlsx, .xls, .csv).
     */
    public function uploadSpreadsheet(Request $request): RedirectResponse
    {
        $request->validate([
            'batch_id' => 'required|string',
            'spreadsheet_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ]);

        $batchId = preg_replace('/[^a-zA-Z0-9_\-]/', '', $request->input('batch_id'));
        $file = $request->file('spreadsheet_file');

        try {
            $candidates = $this->importService->processSpreadsheet($file, $batchId);

            if (empty($candidates)) {
                return redirect()->back()->with('error', 'Spreadsheet tidak memuat data buku atau format baris tidak dapat dikenali.');
            }

            foreach ($candidates as $cand) {
                $this->importService->saveCandidate($batchId, $cand);
            }

            return redirect()->route('admin.books.import.preview', ['batch_id' => $batchId])
                ->with('success', count($candidates) . ' data buku fisik berhasil dibaca dari spreadsheet! Silakan tinjau dan lengkapi data sebelum import.');
        } catch (\Throwable $e) {
            Log::error('Spreadsheet upload error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses file spreadsheet: ' . $e->getMessage());
        }
    }

    /**
     * Display the candidate review/preview page.
     */
    public function preview(Request $request): View|RedirectResponse
    {
        $batchId = $request->input('batch_id');
        if (!$batchId) {
            return redirect()->route('admin.books.import.create')->with('error', 'Batch import tidak ditemukan.');
        }

        $candidates = $this->importService->getBatchCandidates($batchId);

        if (empty($candidates)) {
            return redirect()->route('admin.books.import.create')->with('error', 'Tidak ada data buku kandidat di dalam antrean import ini.');
        }

        $categories = Category::all();
        $locations = Location::all();

        return view('admin.books.import-preview', compact('batchId', 'candidates', 'categories', 'locations'));
    }

    /**
     * Update an individual candidate's cover manually.
     */
    public function replaceCover(Request $request): JsonResponse
    {
        $request->validate([
            'batch_id' => 'required|string',
            'candidate_id' => 'required|string',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $batchId = preg_replace('/[^a-zA-Z0-9_\-]/', '', $request->input('batch_id'));
        $candId = $request->input('candidate_id');
        $candidates = $this->importService->getBatchCandidates($batchId);

        if (!isset($candidates[$candId])) {
            return response()->json(['success' => false, 'message' => 'Candidate tidak ditemukan.'], 404);
        }

        $candidate = $candidates[$candId];
        // Remove old temp cover if any
        if (!empty($candidate['cover_path']) && Storage::disk('public')->exists($candidate['cover_path'])) {
            Storage::disk('public')->delete($candidate['cover_path']);
        }

        $coverFile = $request->file('cover_image');
        $tempDir = 'temp/import/' . $batchId;
        $coverFilename = $candId . '_manual_' . uniqid() . '.' . $coverFile->getClientOriginalExtension();
        Storage::disk('public')->putFileAs($tempDir, $coverFile, $coverFilename);

        $newCoverPath = $tempDir . '/' . $coverFilename;
        $candidate['cover_path'] = $newCoverPath;

        $this->importService->saveCandidate($batchId, $candidate);

        return response()->json([
            'success' => true,
            'cover_url' => asset('storage/' . $newCoverPath),
            'cover_path' => $newCoverPath,
        ]);
    }

    /**
     * Remove an individual candidate and delete its temporary files.
     */
    public function removeCandidate(Request $request): JsonResponse|RedirectResponse
    {
        $batchId = preg_replace('/[^a-zA-Z0-9_\-]/', '', $request->input('batch_id'));
        $candId = $request->input('candidate_id');

        $this->importService->removeCandidateFromBatch($batchId, $candId);
        $remainingCandidates = $this->importService->getBatchCandidates($batchId);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'remaining' => count($remainingCandidates),
            ]);
        }

        if (empty($remainingCandidates)) {
            return redirect()->route('admin.books.import.create')->with('info', 'Semua naskah kandidat telah dihapus.');
        }

        return redirect()->route('admin.books.import.preview', ['batch_id' => $batchId])
            ->with('success', 'Naskah kandidat berhasil dihapus dari antrean import.');
    }

    /**
     * Execute final batch import into database.
     */
    public function store(Request $request)
    {
        $batchId = preg_replace('/[^a-zA-Z0-9_\-]/', '', $request->input('batch_id'));
        $candidates = $this->importService->getBatchCandidates($batchId);

        if (empty($candidates)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sesi import telah kedaluwarsa atau tidak ditemukan.'], 400);
            }
            return redirect()->route('admin.books.import.create')->with('error', 'Sesi import telah kedaluwarsa atau tidak ditemukan.');
        }

        // Merge updated form inputs into candidates array
        $submittedCandidates = $request->input('candidates', []);
        $mergedCandidates = [];

        foreach ($candidates as $id => $origCand) {
            $updated = $submittedCandidates[$id] ?? [];
            $mergedCandidates[$id] = array_merge($origCand, [
                'title' => trim($updated['title'] ?? $origCand['title']),
                'author' => trim($updated['author'] ?? $origCand['author']),
                'category_id' => !empty($updated['category_id']) ? (int)$updated['category_id'] : null,
                'location_id' => !empty($updated['location_id']) ? (int)$updated['location_id'] : null,
                'publisher' => !empty($updated['publisher']) && trim($updated['publisher']) !== '' ? trim($updated['publisher']) : ($origCand['publisher'] ?? null),
                'year' => !empty($updated['year']) ? (int)$updated['year'] : null,
                'isbn' => !empty($updated['isbn']) && trim($updated['isbn']) !== '' ? trim($updated['isbn']) : null,
                'language' => !empty($updated['language']) ? trim($updated['language']) : ($origCand['language'] ?? 'Indonesia'),
                'page_count' => !empty($updated['page_count']) ? (int)$updated['page_count'] : null,
                'stock' => isset($updated['stock']) ? max(0, (int)$updated['stock']) : ($origCand['stock'] ?? 0),
                'description' => !empty($updated['description']) && trim($updated['description']) !== '' ? trim($updated['description']) : null,
            ]);
        }

        $selectedIds = $request->input('selected_ids', array_keys($mergedCandidates));
        if (is_string($selectedIds)) {
            $selectedIds = json_decode($selectedIds, true) ?? array_keys($mergedCandidates);
        }

        $isChunk = $request->boolean('is_chunk', false) || $request->wantsJson() || $request->ajax();
        $isLastChunk = $request->boolean('is_last_chunk', true);

        // Execute import
        $importResult = $this->importService->executeFinalImport($mergedCandidates, $selectedIds);

        // Clean up completed batch temporary files when last chunk finishes
        if ($isLastChunk) {
            $this->importService->cleanupBatch($batchId);
            session()->forget('import_batch_' . $batchId);
        }

        // Return JSON for AJAX chunk requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'imported' => $importResult['imported'],
                'duplicate' => $importResult['duplicate'],
                'failed' => $importResult['failed'],
                'results' => $importResult['results'],
                'is_last_chunk' => $isLastChunk,
                'redirect_url' => route('admin.books.index'),
            ]);
        }

        // Flash detailed summary message for standard form submit
        $successCount = $importResult['imported'];
        $dupCount = $importResult['duplicate'];
        $failCount = $importResult['failed'];

        $summaryMsg = "Proses Import Massal Selesai: {$successCount} buku berhasil diimport ke katalog";
        if ($dupCount > 0) {
            $summaryMsg .= ", {$dupCount} duplikat dilewati";
        }
        if ($failCount > 0) {
            $failedReasons = array_map(fn($f) => "• {$f['title']}: {$f['reason']}", $importResult['results']['failed']);
            $summaryMsg .= ", {$failCount} gagal (" . implode('; ', array_slice($failedReasons, 0, 3)) . ")";
        }
        $summaryMsg .= '.';

        if ($successCount > 0) {
            return redirect()->route('admin.books.index')->with('success', $summaryMsg);
        } else {
            return redirect()->route('admin.books.import.create')->with('error', $summaryMsg);
        }
    }

    /**
     * Download Excel (.xlsx) template for physical books import.
     */
    public function downloadTemplate(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Buku');

        // Header columns (Row 1)
        $headers = [
            'A1' => 'Judul',
            'B1' => 'Penulis',
            'C1' => 'Kategori',
            'D1' => 'Lokasi Rak',
            'E1' => 'Stok',
            'F1' => 'Penerbit',
            'G1' => 'Tahun',
            'H1' => 'ISBN',
            'I1' => 'Bahasa',
            'J1' => 'Halaman',
            'K1' => 'Deskripsi',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style headers: RPK Red (#C62828) background, White Bold Text, Centered
        $headerRange = 'A1:K1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C62828'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'A71D1D'],
                ],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Sample rows with realistic, neatly aligned data
        $sampleData = [
            [
                'Pemrograman Web Modern dengan Laravel & Vue',
                'Budi Santoso',
                'Umum',
                'Rak A1',
                5,
                'Informatika Press',
                2024,
                '9786020298032',
                'Indonesia',
                320,
                'Buku panduan lengkap penguraian dan pengembangan aplikasi web modern.',
            ],
            [
                'Dasar-Dasar Kecerdasan Buatan & Machine Learning',
                'Dr. Irwan Wijaya',
                'Umum',
                'Rak B2',
                3,
                'Sains Media',
                2023,
                '9789792098765',
                'Indonesia',
                280,
                'Pengenalan konsep dasar kecerdasan buatan dan algoritma pembelajarannya.',
            ],
            [
                'Psikologi Kepemimpinan & Manajemen Organisasi',
                'Prof. Hendra Saputra',
                'Psikologi',
                'Rak C3',
                4,
                'Pustaka Akademika',
                2022,
                '9786028765432',
                'Indonesia',
                240,
                'Kajian komprehensif tentang dinamika kepemimpinan dalam organisasi modern.',
            ],
        ];

        $rowNum = 2;
        foreach ($sampleData as $row) {
            $colLetter = 'A';
            foreach ($row as $val) {
                // Ensure ISBN is treated as text so Excel does not format it as scientific notation
                if ($colLetter === 'H') {
                    $sheet->setCellValueExplicit($colLetter . $rowNum, (string)$val, DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValue($colLetter . $rowNum, $val);
                }
                $colLetter++;
            }
            $sheet->getRowDimension($rowNum)->setRowHeight(22);
            $rowNum++;
        }

        // Apply borders and zebra styling to sample rows
        $dataRange = 'A2:K' . ($rowNum - 1);
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E5E5'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Specific column center alignments
        $sheet->getStyle('E2:E' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G2:G' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H2:H' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J2:J' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto-fit column widths
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $headers = [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template_import_buku_fisik.xlsx"',
            'Cache-Control'       => 'max-age=0',
        ];

        return response()->stream(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, $headers);
    }
}
