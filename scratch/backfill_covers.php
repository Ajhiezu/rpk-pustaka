<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Book;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;

echo "=== BACKFILLING MISSING COVERS FOR EXISTING DIGITAL BOOKS ===\n\n";

$booksWithoutCover = Book::whereNull('image')->whereNotNull('pdf_path')->get();
echo "Found " . $booksWithoutCover->count() . " digital books without cover image.\n";

$pdfParser = new PdfParser();

foreach ($booksWithoutCover as $book) {
    echo "Processing Book ID {$book->id}: {$book->title}...\n";
    $fullPdfPath = storage_path('app/private/' . $book->pdf_path);

    if (!file_exists($fullPdfPath)) {
        echo "  ❌ PDF file not found at: {$fullPdfPath}\n";
        continue;
    }

    try {
        // Try parsing PDF pages to verify PDF is valid
        $parsedPdf = $pdfParser->parseFile($fullPdfPath);
        $pages = $parsedPdf->getPages();
        echo "  PDF is valid. Page count: " . count($pages) . "\n";

        // Generate a clean placeholder cover SVG/Image with Book Title and Author if rendering headless
        // Or store a generated high-quality SVG/PNG cover image
        $titleEscaped = htmlspecialchars($book->title);
        $authorEscaped = htmlspecialchars($book->author ?: 'RPK PUSTAKA');
        $codeEscaped = htmlspecialchars($book->book_code);

        $svgContent = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="600" height="840" viewBox="0 0 600 840">
  <rect width="600" height="840" fill="#FFFFFF"/>
  <rect x="20" y="20" width="560" height="800" fill="#F8F8F7" rx="8" stroke="#E5E5E5" stroke-width="2"/>
  <rect x="40" y="40" width="520" height="760" fill="#FFFFFF" rx="4" stroke="#C62828" stroke-width="1.5" stroke-dasharray="6,4"/>
  <circle cx="300" cy="240" r="48" fill="#FEF2F2" stroke="#C62828" stroke-width="2"/>
  <path d="M284 224v32m0-32c-5.832 0-11.246 1.477-16 4v32c4.754-2.523 10.168-4 16-4m0-32c5.832 0 11.246 1.477 16 4v32c-4.754-2.523-10.168-4-16-4m0 0v32" fill="none" stroke="#C62828" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
  <text x="300" y="380" font-family="Inter, sans-serif" font-size="26" font-weight="bold" fill="#181818" text-anchor="middle">{$titleEscaped}</text>
  <text x="300" y="430" font-family="Inter, sans-serif" font-size="16" font-weight="600" fill="#666666" text-anchor="middle">{$authorEscaped}</text>
  <rect x="220" y="480" width="160" height="32" rx="16" fill="#FEF2F2" stroke="#C62828" stroke-width="1"/>
  <text x="300" y="501" font-family="Inter, sans-serif" font-size="12" font-weight="bold" fill="#C62828" text-anchor="middle">NASKAH DIGITAL</text>
  <text x="300" y="740" font-family="Inter, sans-serif" font-size="12" font-weight="bold" fill="#888888" text-anchor="middle">RPK PUSTAKA IMM SAINTEKMU</text>
</svg>
SVG;

        $coverFilename = 'books/cover_auto_' . $book->id . '_' . time() . '.svg';
        Storage::disk('public')->put($coverFilename, $svgContent);
        $book->update(['image' => $coverFilename]);

        echo "  ✓ Generated cover saved to storage/app/public/{$coverFilename}\n";
    } catch (\Throwable $e) {
        echo "  ❌ Error: " . $e->getMessage() . "\n";
    }
}

echo "\n=== BACKFILL COMPLETED ===\n";
