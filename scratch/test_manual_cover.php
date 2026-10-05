<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

echo "=== TESTING MANUAL COVER UPLOAD & RESOLUTION ===\n\n";

$cat = Category::firstOrCreate(['name' => 'Umum'], ['slug' => 'umum']);

// Create a dummy JPG image
$tempJpg = sys_get_temp_dir() . '/dummy_cover.jpg';
file_put_contents($tempJpg, base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA='));

$uploadedFile = new UploadedFile($tempJpg, 'dummy_cover.jpg', 'image/jpeg', null, true);
$storedPath = $uploadedFile->store('books', 'public');

$book = Book::create([
    'book_code' => 'RPK-TEST-' . rand(1000, 9999),
    'title' => 'Buku Manual Sampul Test ' . rand(100, 999),
    'slug' => 'buku-manual-test-' . uniqid(),
    'author' => 'Penulis Test',
    'category_id' => $cat->id,
    'collection_type' => 'fisik',
    'stock' => 5,
    'available_stock' => 5,
    'image' => $storedPath,
]);

echo "Created Book ID: {$book->id}\n";
echo "DB Image Column: {$book->image}\n";
echo "Cover URL Accessor: {$book->cover_url}\n";

$filePublicPath = public_path('storage/' . ltrim(str_replace(['public/', 'storage/'], '', $book->image), '/'));
echo "Public File Path: {$filePublicPath}\n";
echo "Exists on Web Disk: " . (file_exists($filePublicPath) ? 'YES ✓' : 'NO ❌') . "\n";

@unlink($tempJpg);
