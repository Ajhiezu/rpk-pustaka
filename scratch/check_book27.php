<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Book;

$book = Book::find(27);
if ($book) {
    echo "ID: {$book->id}\n";
    echo "Title: {$book->title}\n";
    echo "Image column in DB: " . var_export($book->image, true) . "\n";
    echo "Cover URL: " . var_export($book->cover_url, true) . "\n";
    echo "Collection Type: {$book->collection_type}\n";
    echo "PDF Path: " . var_export($book->pdf_path, true) . "\n";
} else {
    echo "Book 27 not found.\n";
}
