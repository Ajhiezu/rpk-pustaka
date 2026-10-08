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
            // Fetch loan regardless of due_date so we can show the correct error
            $activeLoan = $user ? $this->getDigitalLoan($user->id, $book->id) : null;

            if (!$activeLoan) {
                return redirect()->route('anggota.books.show', $book)
                    ->with('error', 'Anda belum memiliki peminjaman digital untuk buku ini. Silakan ajukan pinjaman digital terlebih dahulu.');
            }

            if ($activeLoan->isExpired()) {
                return redirect()->route('anggota.loans.index')
                    ->with('error', 'Masa peminjaman digital buku "' . $book->title . '" telah berakhir pada ' . $activeLoan->due_date->format('d/m/Y') . '. Ajukan peminjaman baru untuk membaca kembali.');
            }

            if ($activeLoan->status === 'returned') {
                return redirect()->route('anggota.books.show', $book)
                    ->with('error', 'Peminjaman digital buku ini sudah dikembalikan.');
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

        // 2. Strict Access Control — double-check at stream level
        if (!$user->isAdmin()) {
            $activeLoan = $this->getDigitalLoan($user->id, $book->id);

            if (!$activeLoan) {
                abort(403, 'Akses ditolak: Anda belum memiliki peminjaman digital untuk buku ini.');
            }

            if ($activeLoan->isExpired()) {
                abort(403, 'Akses ditolak: Masa peminjaman digital buku ini telah berakhir. Silakan ajukan peminjaman baru.');
            }

            if ($activeLoan->status === 'returned') {
                abort(403, 'Akses ditolak: Peminjaman digital telah selesai dikembalikan.');
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
     * Find any digital loan (active OR expired) for a specific user and book.
     * The caller is responsible for checking isExpired() / status.
     */
    protected function getDigitalLoan(int $userId, int $bookId): ?Loan
    {
        return Loan::where('user_id', $userId)
            ->where('loan_type', 'digital')
            ->whereIn('status', ['borrowed', 'overdue'])
            ->whereHas('loanDetails', function ($q) use ($bookId) {
                $q->where('book_id', $bookId);
            })
            ->latest()
            ->first();
    }
}
