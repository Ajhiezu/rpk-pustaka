<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Category;
use App\Models\Location;
use App\Models\Loan;
use App\Services\LibraryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DomainException;

class PhysicalLoanReservationTest extends TestCase
{
    use RefreshDatabase;

    protected LibraryService $service;
    protected User $memberA;
    protected User $memberB;
    protected Category $category;
    protected Location $location;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(LibraryService::class);

        $this->memberA = User::factory()->create(['role' => 'anggota', 'email' => 'andi@example.com']);
        $this->memberB = User::factory()->create(['role' => 'anggota', 'email' => 'budi@example.com']);

        $this->category = Category::create(['name' => 'Psikologi', 'slug' => 'psikologi']);
        $this->location = Location::create(['name' => 'Rak A1']);
    }

    public function test_1_basic_reservation_decrements_available_stock()
    {
        $book = Book::create([
            'book_code' => 'RPK-B0001',
            'title' => 'Psikologi Sosial',
            'slug' => 'psikologi-sosial-1',
            'author' => 'Andi',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'collection_type' => 'fisik',
            'stock' => 5,
            'available_stock' => 5,
        ]);

        $loan = $this->service->createLoan([
            'user_id' => $this->memberA->id,
            'book_ids' => [$book->id],
            'loan_type' => 'physical',
            'due_date' => now()->addDays(14),
        ]);

        $book->refresh();
        $this->assertEquals(4, $book->available_stock);
        $this->assertEquals(5, $book->stock);
        $this->assertEquals('pending', $loan->status);
        $this->assertNotNull($loan->pickup_deadline);
    }

    public function test_2_approval_does_not_change_stock()
    {
        $book = Book::create([
            'book_code' => 'RPK-B0002',
            'title' => 'Filsafat Ilmu',
            'slug' => 'filsafat-ilmu-2',
            'author' => 'Budi',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'collection_type' => 'fisik',
            'stock' => 5,
            'available_stock' => 5,
        ]);

        $loan = $this->service->createLoan([
            'user_id' => $this->memberA->id,
            'book_ids' => [$book->id],
            'loan_type' => 'physical',
            'due_date' => now()->addDays(14),
        ]);

        $this->service->approveReservation($loan);

        $book->refresh();
        $loan->refresh();

        $this->assertEquals(4, $book->available_stock);
        $this->assertEquals('approved', $loan->status);
        $this->assertNotNull($loan->approved_at);
    }

    public function test_3_handover_borrowed_does_not_change_stock()
    {
        $book = Book::create([
            'book_code' => 'RPK-B0003',
            'title' => 'Logika Modern',
            'slug' => 'logika-modern-3',
            'author' => 'Cahyo',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'collection_type' => 'fisik',
            'stock' => 5,
            'available_stock' => 5,
        ]);

        $loan = $this->service->createLoan([
            'user_id' => $this->memberA->id,
            'book_ids' => [$book->id],
            'loan_type' => 'physical',
            'due_date' => now()->addDays(14),
        ]);

        $this->service->approveReservation($loan);
        $this->service->handoverLoan($loan);

        $book->refresh();
        $loan->refresh();

        $this->assertEquals(4, $book->available_stock);
        $this->assertEquals('borrowed', $loan->status);
        $this->assertNotNull($loan->borrowed_at);
    }

    public function test_4_return_restores_available_stock()
    {
        $book = Book::create([
            'book_code' => 'RPK-B0004',
            'title' => 'Sosiologi Agama',
            'slug' => 'sosiologi-agama-4',
            'author' => 'Dedi',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'collection_type' => 'fisik',
            'stock' => 5,
            'available_stock' => 5,
        ]);

        $loan = $this->service->createLoan([
            'user_id' => $this->memberA->id,
            'book_ids' => [$book->id],
            'loan_type' => 'physical',
            'due_date' => now()->addDays(14),
        ]);

        $this->service->approveReservation($loan);
        $this->service->handoverLoan($loan);
        $this->service->processReturn($loan, ['condition' => 'good']);

        $book->refresh();
        $loan->refresh();

        $this->assertEquals(5, $book->available_stock);
        $this->assertEquals('returned', $loan->status);
    }

    public function test_5_member_cancellation_restores_available_stock()
    {
        $book = Book::create([
            'book_code' => 'RPK-B0005',
            'title' => 'Kritik Seni',
            'slug' => 'kritik-seni-5',
            'author' => 'Eko',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'collection_type' => 'fisik',
            'stock' => 5,
            'available_stock' => 5,
        ]);

        $loan = $this->service->createLoan([
            'user_id' => $this->memberA->id,
            'book_ids' => [$book->id],
            'loan_type' => 'physical',
            'due_date' => now()->addDays(14),
        ]);

        $this->assertEquals(4, $book->fresh()->available_stock);

        $this->service->cancelReservation($loan);

        $this->assertEquals(5, $book->fresh()->available_stock);
        $this->assertEquals('cancelled', $loan->fresh()->status);
    }

    public function test_6_admin_rejection_restores_available_stock()
    {
        $book = Book::create([
            'book_code' => 'RPK-B0006',
            'title' => 'Etika Publik',
            'slug' => 'etika-publik-6',
            'author' => 'Fajar',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'collection_type' => 'fisik',
            'stock' => 5,
            'available_stock' => 5,
        ]);

        $loan = $this->service->createLoan([
            'user_id' => $this->memberA->id,
            'book_ids' => [$book->id],
            'loan_type' => 'physical',
            'due_date' => now()->addDays(14),
        ]);

        $this->service->rejectReservation($loan, 'Perawatan fisik');

        $this->assertEquals(5, $book->fresh()->available_stock);
        $this->assertEquals('rejected', $loan->fresh()->status);
        $this->assertEquals('Perawatan fisik', $loan->fresh()->rejection_reason);
    }

    public function test_7_overdue_expiry_restores_available_stock()
    {
        $book = Book::create([
            'book_code' => 'RPK-B0007',
            'title' => 'Bioinformatika',
            'slug' => 'bioinformatika-7',
            'author' => 'Gita',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'collection_type' => 'fisik',
            'stock' => 5,
            'available_stock' => 5,
        ]);

        $loan = $this->service->createLoan([
            'user_id' => $this->memberA->id,
            'book_ids' => [$book->id],
            'loan_type' => 'physical',
            'due_date' => now()->addDays(14),
        ]);

        // Manually update pickup_deadline to past
        $loan->update(['pickup_deadline' => now()->subHour()]);

        $this->service->expireAllOverdueReservations();

        $this->assertEquals(5, $book->fresh()->available_stock);
        $this->assertEquals('expired', $loan->fresh()->status);
    }

    public function test_8_last_stock_concurrency_lock_prevents_double_booking()
    {
        $book = Book::create([
            'book_code' => 'RPK-B0008',
            'title' => 'Buku Terakhir',
            'slug' => 'buku-terakhir-8',
            'author' => 'Hendra',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'collection_type' => 'fisik',
            'stock' => 1,
            'available_stock' => 1,
        ]);

        // Andi reserves last stock
        $loanA = $this->service->createLoan([
            'user_id' => $this->memberA->id,
            'book_ids' => [$book->id],
            'loan_type' => 'physical',
            'due_date' => now()->addDays(14),
        ]);

        $this->assertEquals(0, $book->fresh()->available_stock);

        // Budi attempts to reserve same book -> expects DomainException
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('tidak tersedia untuk dipesan saat ini');

        $this->service->createLoan([
            'user_id' => $this->memberB->id,
            'book_ids' => [$book->id],
            'loan_type' => 'physical',
            'due_date' => now()->addDays(14),
        ]);
    }

    public function test_9_double_cancellation_idempotency()
    {
        $book = Book::create([
            'book_code' => 'RPK-B0009',
            'title' => 'Hukum Perdata',
            'slug' => 'hukum-perdata-9',
            'author' => 'Indra',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'collection_type' => 'fisik',
            'stock' => 1,
            'available_stock' => 1,
        ]);

        $loan = $this->service->createLoan([
            'user_id' => $this->memberA->id,
            'book_ids' => [$book->id],
            'loan_type' => 'physical',
            'due_date' => now()->addDays(14),
        ]);

        $this->service->cancelReservation($loan);
        $this->assertEquals(1, $book->fresh()->available_stock);

        // Cancelling again should throw exception and NOT add +1 stock
        try {
            $this->service->cancelReservation($loan->fresh());
        } catch (DomainException $e) {
            // Expected
        }

        $this->assertEquals(1, $book->fresh()->available_stock);
    }

    public function test_10_invalid_transition_cancelled_to_approved_fails()
    {
        $book = Book::create([
            'book_code' => 'RPK-B0010',
            'title' => 'Statistika Terapan',
            'slug' => 'statistika-terapan-10',
            'author' => 'Joni',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'collection_type' => 'fisik',
            'stock' => 1,
            'available_stock' => 1,
        ]);

        $loan = $this->service->createLoan([
            'user_id' => $this->memberA->id,
            'book_ids' => [$book->id],
            'loan_type' => 'physical',
            'due_date' => now()->addDays(14),
        ]);

        $this->service->cancelReservation($loan);

        $this->expectException(DomainException::class);
        $this->service->approveReservation($loan->fresh());
    }

    public function test_11_ownership_authorization_on_show()
    {
        $book = Book::create([
            'book_code' => 'RPK-B0011',
            'title' => 'Kalkulus Lanjut',
            'slug' => 'kalkulus-lanjut-11',
            'author' => 'Kiki',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'collection_type' => 'fisik',
            'stock' => 1,
            'available_stock' => 1,
        ]);

        $loan = $this->service->createLoan([
            'user_id' => $this->memberA->id,
            'book_ids' => [$book->id],
            'loan_type' => 'physical',
            'due_date' => now()->addDays(14),
        ]);

        // Member B tries to access Member A's loan -> expects 403 Forbidden
        $response = $this->actingAs($this->memberB)->get(route('anggota.loans.show', $loan));
        $response->assertStatus(403);
    }
}
