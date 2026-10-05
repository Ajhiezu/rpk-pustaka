<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Loan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookReaderController extends Controller
{
    /**
     * Display the web-based PDF reader page.
     */
    public function reader(Book $book)
    {
        $user = Auth::user();

        // 1. Verify digital availability
        if (!$book->hasDigital() || !Storage::disk('local')->exists($book->pdf_path)) {
            $fallbackRoute = ($user && $user->isAdmin()) ? 'admin.books.show' : 'anggota.books.show';
            return redirect()->route($fallbackRoute, $book)
                ->with('error', 'Versi digital naskah buku ini belum tersedia di server.');
        }

        // 2. Authorization & Loan Validation
        $activeLoan = null;
        if ($user && $user->isAdmin()) {
            // Admin can preview digital books anytime
            $activeLoan = null;
        } else {
            $activeLoan = $user ? $this->getActiveDigitalLoan($user->id, $book->id) : null;

            if (!$activeLoan) {
                return redirect()->route('anggota.books.show', $book)
                    ->with('error', 'Anda belum memiliki peminjaman digital aktif untuk buku ini. Silakan ajukan pinjaman digital terlebih dahulu.');
            }

            if ($activeLoan->isExpired()) {
                return redirect()->route('anggota.books.show', $book)
                    ->with('error', 'Masa peminjaman digital buku ini telah berakhir.');
            }
        }

        $streamUrl = ($user && $user->isAdmin())
            ? route('admin.books.stream', $book)
            : route('anggota.books.stream', $book);

        $backUrl = ($user && $user->isAdmin())
            ? route('admin.books.show', $book)
            : route('anggota.books.show', $book);

        return view('member.books.reader', compact('book', 'activeLoan', 'streamUrl', 'backUrl'));
    }

    /**
     * Stream protected PDF inline without exposing server path.
     */
    public function stream(Book $book)
    {
        $user = Auth::user();

        // 1. Verify digital availability
        if (!$book->hasDigital() || !Storage::disk('local')->exists($book->pdf_path)) {
            abort(404, 'File digital tidak ditemukan.');
        }

        // 2. Strict Access Control
        if (!$user->isAdmin()) {
            $activeLoan = $this->getActiveDigitalLoan($user->id, $book->id);

            if (!$activeLoan) {
                abort(403, 'Akses ditolak: Anda belum memiliki peminjaman digital aktif.');
            }

            if ($activeLoan->isExpired()) {
                abort(403, 'Akses ditolak: Masa peminjaman digital telah berakhir.');
            }

            if ($activeLoan->status === 'returned') {
                abort(403, 'Akses ditolak: Peminjaman telah selesai dikembalikan.');
            }
        }

        // 3. Stream from private storage
        $disk = Storage::disk('local');
        $filePath = $book->pdf_path;
        $fileSize = $disk->size($filePath);

        return new StreamedResponse(function () use ($disk, $filePath) {
            $stream = $disk->readStream($filePath);
            if ($stream) {
                fpassthru($stream);
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
            'Content-Length' => $fileSize,
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Find active digital loan for a specific user and book.
     */
    protected function getActiveDigitalLoan(int $userId, int $bookId): ?Loan
    {
        return Loan::where('user_id', $userId)
            ->where('loan_type', 'digital')
            ->where('status', 'borrowed')
            ->whereDate('due_date', '>=', now()->toDateString())
            ->whereHas('loanDetails', function ($q) use ($bookId) {
                $q->where('book_id', $bookId);
            })
            ->latest()
            ->first();
    }
}
