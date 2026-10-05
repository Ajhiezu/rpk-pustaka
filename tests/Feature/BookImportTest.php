<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use App\Services\BookImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $member;
    protected Category $category;
    protected Location $location;
    protected BookImportService $importService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importService = new BookImportService();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@imm-saintek.org',
        ]);

        $this->member = User::factory()->create([
            'role' => 'anggota',
            'email' => 'member@imm-saintek.org',
        ]);

        $this->category = Category::create([
            'name' => 'Psikologi',
            'slug' => 'psikologi',
        ]);

        $this->location = Location::create([
            'name' => 'Rak A-01',
            'description' => 'Lantai 1 Sayap Barat',
        ]);
    }

    public function test_non_admin_cannot_access_import_routes()
    {
        $response = $this->actingAs($this->member)->get(route('admin.books.import.create'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_import_upload_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.books.import.create'));
        $response->assertStatus(200);
        $response->assertSee('Import Massal Koleksi Buku');
        $response->assertSee('Import Buku Digital');
        $response->assertSee('Import Buku Fisik');
    }

    public function test_filename_is_normalized_properly_to_candidate_title()
    {
        $this->assertEquals('Psikologi Gelap', $this->importService->normalizeFilenameToTitle('Psikologi-Gelap.pdf'));
        $this->assertEquals('6 Prinsip Dan 7 Strategi Persuasi', $this->importService->normalizeFilenameToTitle('6-Prinsip-dan-7-Strategi-Persuasi.pdf'));
        $this->assertEquals('Dasar Manajemen Organisasi', $this->importService->normalizeFilenameToTitle('Dasar_Manajemen_Organisasi.docx'));
    }

    public function test_duplicate_detection_identifies_existing_isbn_and_title()
    {
        Book::create([
            'book_code' => 'RPK-B0001',
            'title' => 'Psikologi Komunikasi',
            'slug' => 'psikologi-komunikasi-test',
            'author' => 'Jalaluddin Rakhmat',
            'category_id' => $this->category->id,
            'collection_type' => 'digital',
            'isbn' => '978-602-1234-56-7',
            'stock' => 0,
            'available_stock' => 0,
        ]);

        // Duplicate ISBN check
        $dupIsbn = $this->importService->checkDuplicate('Buku Lain', 'Penulis Lain', '978-602-1234-56-7');
        $this->assertTrue($dupIsbn['is_duplicate']);
        $this->assertStringContainsString('Duplikat ISBN', $dupIsbn['message']);

        // Duplicate Title check
        $dupTitle = $this->importService->checkDuplicate('psikologi komunikasi', 'Seseorang', null);
        $this->assertTrue($dupTitle['is_duplicate']);
        $this->assertStringContainsString('Kemungkinan Duplikat', $dupTitle['message']);

        // Non duplicate
        $noDup = $this->importService->checkDuplicate('Filsafat Ilmu', 'Profesor X', '978-000-0000-00-0');
        $this->assertFalse($noDup['is_duplicate']);
    }

    public function test_execute_final_import_handles_partial_success_and_transactions()
    {
        Storage::fake('local');
        Storage::fake('public');

        $batchId = Str::uuid()->toString();
        $tempDir = 'temp/import/' . $batchId;

        // Candidate 1: Valid digital book
        $cand1Id = 'cand_01';
        $pdfPath1 = $tempDir . '/' . $cand1Id . '.pdf';
        Storage::disk('local')->put($pdfPath1, '%PDF-1.4 dummy content');

        $coverPath1 = $tempDir . '/' . $cand1Id . '_cover.webp';
        Storage::disk('public')->put($coverPath1, 'fake webp data');

        $candidates = [
            $cand1Id => [
                'id' => $cand1Id,
                'title' => 'Psikologi Gelap',
                'author' => 'James W. Williams',
                'category_id' => $this->category->id,
                'collection_type' => 'digital',
                'file_path' => $pdfPath1,
                'cover_path' => $coverPath1,
                'publisher' => 'Pustaka Saintek',
                'year' => 2021,
                'isbn' => null,
                'language' => 'Indonesia',
                'page_count' => 128,
                'description' => 'Buku psikologi terapan.',
            ],
            'cand_02' => [
                'id' => 'cand_02',
                'title' => 'Buku Tanpa Kategori',
                'author' => 'Penulis A',
                'category_id' => null, // Invalid
                'collection_type' => 'digital',
                'file_path' => $pdfPath1,
            ],
        ];

        $result = $this->importService->executeFinalImport($candidates);

        $this->assertEquals(2, $result['total']);
        $this->assertEquals(1, $result['imported']);
        $this->assertEquals(1, $result['failed']);

        // Check that valid book was created with generated book_code
        $this->assertDatabaseHas('books', [
            'title' => 'Psikologi Gelap',
            'author' => 'James W. Williams',
            'collection_type' => 'digital',
            'category_id' => $this->category->id,
        ]);

        $createdBook = Book::where('title', 'Psikologi Gelap')->first();
        $this->assertNotNull($createdBook->book_code);
        $this->assertStringStartsWith('RPK-B', $createdBook->book_code);
        $this->assertNotNull($createdBook->pdf_path);
        $this->assertStringStartsWith('books/pdf/', $createdBook->pdf_path);
        $this->assertTrue(Storage::disk('local')->exists($createdBook->pdf_path));
    }

    public function test_admin_can_upload_digital_batch_and_receive_candidate_json()
    {
        Storage::fake('local');
        Storage::fake('public');

        $batchId = Str::uuid()->toString();
        $fakePdf = UploadedFile::fake()->create('Psikologi-Gelap.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->admin)->postJson(route('admin.books.import.digital'), [
            'batch_id' => $batchId,
            'files' => [$fakePdf],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'batch_id' => $batchId,
            'total_candidates' => 1,
        ]);

        $sessionKey = 'import_batch_' . $batchId;
        $batch = session($sessionKey);
        $this->assertNotNull($batch);
        $this->assertCount(1, $batch['candidates']);
        
        $candidate = reset($batch['candidates']);
        $this->assertEquals('Psikologi Gelap', $candidate['title']);
    }

    public function test_admin_can_view_preview_page_with_candidates()
    {
        $batchId = Str::uuid()->toString();
        $sessionKey = 'import_batch_' . $batchId;

        session([
            $sessionKey => [
                'batch_id' => $batchId,
                'created_at' => time(),
                'candidates' => [
                    'cand_01' => [
                        'id' => 'cand_01',
                        'original_filename' => 'Psikologi-Gelap.pdf',
                        'file_path' => 'temp/import/' . $batchId . '/cand_01.pdf',
                        'file_hash' => 'dummy_hash',
                        'cover_path' => null,
                        'collection_type' => 'digital',
                        'title' => 'Psikologi Gelap',
                        'author' => 'James W. Williams',
                        'category_id' => $this->category->id,
                        'location_id' => null,
                        'stock' => 0,
                        'available_stock' => 0,
                        'publisher' => 'Pustaka',
                        'year' => 2021,
                        'isbn' => null,
                        'language' => 'Indonesia',
                        'page_count' => 120,
                        'description' => 'Sinopsis',
                        'status' => 'VALID',
                        'status_messages' => [],
                    ]
                ]
            ]
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.books.import.preview', ['batch_id' => $batchId]));
        $response->assertStatus(200);
        $response->assertSee('Pratinjau');
        $response->assertSee('Validasi Import Massal');
        $response->assertSee('Psikologi Gelap');
        $response->assertSee('James W. Williams');
    }

    public function test_admin_can_replace_cover_and_remove_candidate()
    {
        Storage::fake('local');
        Storage::fake('public');

        $batchId = Str::uuid()->toString();
        $sessionKey = 'import_batch_' . $batchId;
        $tempDir = 'temp/import/' . $batchId;

        $pdfPath = $tempDir . '/cand_01.pdf';
        Storage::disk('local')->put($pdfPath, 'dummy pdf');

        session([
            $sessionKey => [
                'batch_id' => $batchId,
                'created_at' => time(),
                'candidates' => [
                    'cand_01' => [
                        'id' => 'cand_01',
                        'original_filename' => 'Buku-Test.pdf',
                        'file_path' => $pdfPath,
                        'file_hash' => 'dummy_hash',
                        'cover_path' => null,
                        'collection_type' => 'digital',
                        'title' => 'Buku Test',
                        'author' => 'Penulis Test',
                        'category_id' => $this->category->id,
                        'location_id' => null,
                        'stock' => 0,
                        'available_stock' => 0,
                        'status' => 'VALID',
                        'status_messages' => [],
                    ]
                ]
            ]
        ]);

        // Replace Cover
        $fakeCover = UploadedFile::fake()->image('cover.jpg', 300, 400);
        $coverResponse = $this->actingAs($this->admin)->postJson(route('admin.books.import.cover'), [
            'batch_id' => $batchId,
            'candidate_id' => 'cand_01',
            'cover_image' => $fakeCover,
        ]);

        $coverResponse->assertStatus(200);
        $coverResponse->assertJson(['success' => true]);

        // Remove Candidate
        $removeResponse = $this->actingAs($this->admin)->postJson(route('admin.books.import.remove'), [
            'batch_id' => $batchId,
            'candidate_id' => 'cand_01',
        ]);

        $removeResponse->assertStatus(200);
        $removeResponse->assertJson(['success' => true, 'remaining' => 0]);
        $this->assertFalse(Storage::disk('local')->exists($pdfPath));
    }
}
