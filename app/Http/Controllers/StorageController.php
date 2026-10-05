<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageController extends Controller
{
    /**
     * Serve public storage files directly from storage/app/public
     * Workaround for shared hosting environments where symlinks / exec() are disabled.
     */
    public function show(string $path): BinaryFileResponse
    {
        $path = ltrim($path, '/\\');

        // Prevent path traversal attacks
        if (str_contains($path, '..') || str_contains($path, '\\')) {
            abort(404);
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($path)) {
            abort(404);
        }

        $fullPath = $disk->path($path);
        $realStoragePath = realpath($disk->path(''));
        $realFilePath = realpath($fullPath);

        // Security check: ensure path resolves strictly inside storage/app/public and is a valid file
        if (!$realFilePath || !$realStoragePath || !str_starts_with($realFilePath, $realStoragePath) || !is_file($realFilePath)) {
            abort(404);
        }

        return response()->file($realFilePath);
    }
}
