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
     * Download CSV template for physical books import.
     */
    public function downloadTemplate(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_buku_fisik.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($handle, [
                'judul',
                'penulis',
                'kategori',
                'lokasi',
                'stok',
                'penerbit',
                'tahun',
                'isbn',
                'bahasa',
                'halaman',
                'deskripsi'
            ]);

            // Sample rows with realistic data
            fputcsv($handle, [
                'Pemrograman Web Modern dengan Laravel & Vue',
                'Budi Santoso',
                'Teknologi',
                'Rak A1',
                '5',
                'Informatika Press',
                '2024',
                '9786020298032',
                'Indonesia',
                '320',
                'Buku panduan lengkap penguraian dan pengembangan aplikasi web modern.'
            ]);

            fputcsv($handle, [
                'Dasar-Dasar Kecerdasan Buatan & Machine Learning',
                'Dr. Irwan Wijaya',
                'Sains',
                'Rak B2',
                '3',
                'Sains Media',
                '2023',
                '9789792098765',
                'Indonesia',
                '280',
                'Pengenalan konsep dasar kecerdasan buatan dan algoritma pembelajarannya.'
            ]);

            fclose($handle);
        }, 200, $headers);
    }
}
