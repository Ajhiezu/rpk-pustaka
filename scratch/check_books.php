<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Book;

$books = Book::latest()->take(10)->get();
foreach ($books as $b) {
    echo "ID: {$b->id} | Title: {$b->title} | Image: " . ($b->image ?? 'NULL') . "\n";
    if ($b->image) {
        $fullPath = storage_path('app/public/' . $b->image);
        $existsInStorage = file_exists($fullPath);
        $publicPath = public_path('storage/' . $b->image);
        $existsInPublic = file_exists($publicPath);
        echo "   Storage Path: {$fullPath} (Exists: " . ($existsInStorage ? 'YES' : 'NO') . ")\n";
        echo "   Public Link Path: {$publicPath} (Exists: " . ($existsInPublic ? 'YES' : 'NO') . ")\n";
    }
}
