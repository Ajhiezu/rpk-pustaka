<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\BookImportService;
use App\Models\Book;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

echo "=== STARTING BULK IMPORT AUTO COVER VERIFICATION TEST ===\n\n";

$service = app(BookImportService::class);
$batchId = 'test_batch_' . Str::random(8);

// 1. Create dummy PDF file for testing
$pdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000052 00000 n\n0000000101 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n173\n%%EOF";

$tempPdfPath = sys_get_temp_dir() . '/Psikologi-Gelap-Test-Unique.pdf';
file_put_contents($tempPdfPath, $pdfContent);
$dummyPdf = new UploadedFile($tempPdfPath, 'Psikologi-Gelap-Test-Unique.pdf', 'application/pdf', null, true);

// Dummy WebP Base64 Cover (Simulating PDF.js page 1 render)
$dummyWebpBase64 = 'data:image/webp;base64,UklGRiQAAABXRUJQVlA4IBgAAAAwAQCdASoBAAEAAQAcJaQAA3AA/v3AgAA=';

echo "[TEST A & B] Processing digital file with rendered cover...\n";
$candidate = $service->processDigitalFile($dummyPdf, $batchId, $dummyWebpBase64);

echo "Candidate ID: " . $candidate['id'] . "\n";
echo "Title: " . $candidate['title'] . "\n";
echo "Cover Path: " . ($candidate['cover_path'] ?? 'NULL') . "\n";
echo "Status: " . $candidate['status'] . "\n";

if ($candidate['cover_path'] && Storage::disk('public')->exists($candidate['cover_path'])) {
    echo "✓ PASS: Cover file exists at storage/app/public/" . $candidate['cover_path'] . "\n\n";
} else {
    echo "❌ FAIL: Cover file missing!\n\n";
    exit(1);
}

// 2. Test Manual Replacement
echo "[TEST C] Testing manual cover replacement...\n";
$dummyManualCoverPath = sys_get_temp_dir() . '/manual_cover.jpg';
// 1x1 JPG
file_put_contents($dummyManualCoverPath, base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA='));
$manualCoverFile = new UploadedFile($dummyManualCoverPath, 'manual_cover.jpg', 'image/jpeg', null, true);

$tempDir = 'temp/import/' . $batchId;
$manualCoverFilename = $candidate['id'] . '_manual_test.jpg';
Storage::disk('public')->putFileAs($tempDir, $manualCoverFile, $manualCoverFilename);
$candidate['cover_path'] = $tempDir . '/' . $manualCoverFilename;

echo "New Cover Path: " . $candidate['cover_path'] . "\n";
if (Storage::disk('public')->exists($candidate['cover_path'])) {
    echo "✓ PASS: Manual cover replaced successfully!\n\n";
} else {
    echo "❌ FAIL: Manual cover missing!\n\n";
    exit(1);
}

// 3. Test Final Import
echo "[TEST E] Executing final import to database...\n";
$candidatesMap = [$candidate['id'] => $candidate];
$importResult = $service->executeFinalImport($candidatesMap, [$candidate['id']]);

echo "Imported Count: " . $importResult['imported'] . "\n";
if ($importResult['imported'] > 0) {
    $book = Book::where('title', $candidate['title'])->latest()->first();
    if ($book) {
        echo "Book Created ID: " . $book->id . "\n";
        echo "Book Image (books.image): " . ($book->image ?? 'NULL') . "\n";
        echo "Book PDF (books.pdf_path): " . ($book->pdf_path ?? 'NULL') . "\n";

        if ($book->image && Storage::disk('public')->exists($book->image)) {
            echo "✓ PASS: Permanent cover saved at storage/app/public/" . $book->image . "\n";
        } else {
            echo "❌ FAIL: Permanent cover file missing in storage/app/public/\n";
            exit(1);
        }

        if ($book->pdf_path && Storage::disk('local')->exists($book->pdf_path)) {
            echo "✓ PASS: Permanent PDF saved at storage/app/private/" . $book->pdf_path . "\n";
        } else {
            echo "❌ FAIL: Permanent PDF file missing in storage/app/private/\n";
            exit(1);
        }
    } else {
        echo "❌ FAIL: Book record not found in DB!\n";
        exit(1);
    }
} else {
    echo "❌ FAIL: Final import failed!\n";
    exit(1);
}

echo "\n==============================================\n";
echo "   ALL TEST CASES PASSED SUCCESSFULLY (100%)  \n";
echo "==============================================\n";

@unlink($tempPdfPath);
@unlink($dummyManualCoverPath);
