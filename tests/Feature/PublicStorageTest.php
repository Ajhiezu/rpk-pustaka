<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;

class PublicStorageTest extends TestCase
{
    public function test_can_access_public_storage_file(): void
    {
        Storage::disk('public')->put('test.txt', 'RPK PUSTAKA Storage Workaround - OK');

        $response = $this->get('/storage/test.txt');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');

        $baseResponse = $response->baseResponse;
        if ($baseResponse instanceof BinaryFileResponse) {
            $this->assertStringContainsString('test.txt', $baseResponse->getFile()->getFilename());
            $this->assertEquals('RPK PUSTAKA Storage Workaround - OK', trim(file_get_contents($baseResponse->getFile()->getPathname())));
        }
    }

    public function test_returns_404_for_non_existent_file(): void
    {
        $response = $this->get('/storage/non_existent_file_12345.txt');

        $response->assertStatus(404);
    }

    public function test_prevents_path_traversal(): void
    {
        $response = $this->get('/storage/../.env');

        $response->assertStatus(404);
    }
}
