<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\BookImportController as AdminBookImportController;
use App\Http\Controllers\Admin\MemberImportController as AdminMemberImportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\EssayController as AdminEssayController;
use App\Http\Controllers\Officer\LoanController as OfficerLoanController;

use App\Http\Controllers\Anggota\BookController as AnggotaBookController;
use App\Http\Controllers\Anggota\LoanController as AnggotaLoanController;
use App\Http\Controllers\Anggota\BookReaderController as AnggotaReaderController;
use App\Http\Controllers\Anggota\ArticleController as AnggotaArticleController;
use App\Http\Controllers\Anggota\EssayController as AnggotaEssayController;
use App\Http\Controllers\StorageController;

// Public Landing Page
Route::get('/', function () {
    $books = \App\Models\Book::with(['category', 'location'])->latest()->limit(8)->get();
    
    // Ambil pencarian populer dari kategori yang memiliki koleksi buku
    $popularSearches = \App\Models\Category::whereHas('books')
        ->withCount('books')
        ->having('books_count', '>', 0)
        ->orderByDesc('books_count')
        ->limit(4)
        ->pluck('name');

    $latestArticles = \App\Models\Article::published()->with('user')->latest('published_at')->limit(3)->get();
    $publishedEssays = \App\Models\Essay::published()->with('user')->latest()->limit(3)->get();

    return view('welcome', compact('books', 'popularSearches', 'latestArticles', 'publishedEssays'));
})->name('home');

// Public Book Detail
Route::get('/catalog/{book}', [AnggotaBookController::class, 'show'])->name('public.books.show');

// Public Published Articles
Route::get('/articles', [AnggotaArticleController::class, 'index'])->name('public.articles.index');
Route::get('/articles/{article:slug}', [AnggotaArticleController::class, 'show'])->name('public.articles.show');

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');

    // ==========================================
    // ADMIN ROUTES (Otoritas Pengelola Pustaka)
    // ==========================================
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Master Data & Import
        Route::get('books/import', [AdminBookImportController::class, 'create'])->name('books.import.create');
        Route::post('books/import/digital', [AdminBookImportController::class, 'uploadDigital'])->name('books.import.digital');
        Route::post('books/import/spreadsheet', [AdminBookImportController::class, 'uploadSpreadsheet'])->name('books.import.spreadsheet');
        Route::get('books/import/preview', [AdminBookImportController::class, 'preview'])->name('books.import.preview');
        Route::post('books/import/cover', [AdminBookImportController::class, 'replaceCover'])->name('books.import.cover');
        Route::post('books/import/remove', [AdminBookImportController::class, 'removeCandidate'])->name('books.import.remove');
        Route::post('books/import/store', [AdminBookImportController::class, 'store'])->name('books.import.store');
        Route::post('books/{book}/auto-cover', [AdminBookController::class, 'saveAutoCover'])->name('books.auto-cover');
        Route::resource('books', AdminBookController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('locations', LocationController::class);
        Route::resource('users', AdminUserController::class);

        // Import Massal Data Anggota
        Route::get('members/import', [AdminMemberImportController::class, 'create'])->name('members.import.create');
        Route::post('members/import/preview', [AdminMemberImportController::class, 'preview'])->name('members.import.preview');
        Route::post('members/import/store', [AdminMemberImportController::class, 'store'])->name('members.import.store');
        Route::get('members/import/template', [AdminMemberImportController::class, 'downloadTemplate'])->name('members.import.template');

        // Sirkulasi Peminjaman & Pengembalian (Digital + Fisik)
        Route::post('loans/{loan}/approve', [OfficerLoanController::class, 'approve'])->name('loans.approve');
        Route::post('loans/{loan}/reject', [OfficerLoanController::class, 'reject'])->name('loans.reject');
        Route::post('loans/{loan}/handover', [OfficerLoanController::class, 'handover'])->name('loans.handover');
        Route::post('loans/{loan}/return', [OfficerLoanController::class, 'returnBook'])->name('loans.returnBook');
        Route::post('loans/{loan}/pay-fine', [OfficerLoanController::class, 'payFine'])->name('loans.payFine');
        Route::resource('loans', OfficerLoanController::class);

        // Modul Artikel (Admin CMS)
        Route::post('articles/{article}/toggle-publish', [AdminArticleController::class, 'togglePublish'])->name('articles.toggle');
        Route::resource('articles', AdminArticleController::class);

        // Modul Esai / Tulisan Anggota (Review & Moderation)
        Route::get('essays', [AdminEssayController::class, 'index'])->name('essays.index');
        Route::get('essays/{essay}', [AdminEssayController::class, 'show'])->name('essays.show');
        Route::post('essays/{essay}/status', [AdminEssayController::class, 'updateStatus'])->name('essays.updateStatus');
        Route::get('essays/{essay}/download', [AdminEssayController::class, 'downloadFile'])->name('essays.download');

        // Laporan & Rekapitulasi
        Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export/pdf', [AdminReportController::class, 'exportPdf'])->name('reports.pdf');
        Route::get('reports/export/excel', [AdminReportController::class, 'exportExcel'])->name('reports.excel');

        // Reader Digital Khusus Admin (Pratinjau & Kurasi Naskah)
        Route::get('books/{book}/reader', [AnggotaReaderController::class, 'reader'])->name('books.reader');
        Route::get('books/{book}/stream', [AnggotaReaderController::class, 'stream'])->name('books.stream');

        // Pengaturan Sistem
        Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [AdminSettingController::class, 'update'])->name('settings.update');
    });

    // ==========================================
    // ANGGOTA ROUTES (Layanan Anggota Pustaka)
    // ==========================================
    Route::middleware(['role:anggota'])->prefix('anggota')->name('anggota.')->group(function () {
        // Katalog & Detail Koleksi
        Route::get('books', [AnggotaBookController::class, 'index'])->name('books.index');
        Route::get('books/{book}', [AnggotaBookController::class, 'show'])->name('books.show');

        // Reader Digital Terproteksi (Akses PDF Private Stream)
        Route::get('books/{book}/reader', [AnggotaReaderController::class, 'reader'])->name('books.reader');
        Route::get('books/{book}/stream', [AnggotaReaderController::class, 'stream'])->name('books.stream');

        // Pengajuan Peminjaman (Digital & Fisik) & Reservasi
        Route::post('loans', [AnggotaLoanController::class, 'store'])->name('loans.store');
        Route::get('my-loans', [AnggotaLoanController::class, 'index'])->name('loans.index');
        Route::get('loans/{loan}', [AnggotaLoanController::class, 'show'])->name('loans.show');
        Route::post('loans/{loan}/cancel', [AnggotaLoanController::class, 'cancel'])->name('loans.cancel');

        // Artikel Terbit
        Route::get('articles', [AnggotaArticleController::class, 'index'])->name('articles.index');
        Route::get('articles/{article:slug}', [AnggotaArticleController::class, 'show'])->name('articles.show');

        // Tulisan & Esai Anggota
        Route::get('essays', [AnggotaEssayController::class, 'index'])->name('essays.index');
        Route::get('essays/create', [AnggotaEssayController::class, 'create'])->name('essays.create');
        Route::post('essays', [AnggotaEssayController::class, 'store'])->name('essays.store');
        Route::get('essays/{essay}', [AnggotaEssayController::class, 'show'])->name('essays.show');
        Route::get('essays/{essay}/download', [AnggotaEssayController::class, 'downloadFile'])->name('essays.download');
    });

    // ==========================================
    // BACKWARD COMPATIBILITY ALIASES
    // ==========================================

    // Legacy 'member.*' route name aliases for existing views
    Route::middleware(['role:anggota'])->prefix('member')->name('member.')->group(function () {
        Route::get('books', [AnggotaBookController::class, 'index'])->name('books.index');
        Route::get('books/{book}', [AnggotaBookController::class, 'show'])->name('books.show');
        Route::get('books/{book}/reader', [AnggotaReaderController::class, 'reader'])->name('books.reader');
        Route::get('books/{book}/stream', [AnggotaReaderController::class, 'stream'])->name('books.stream');
        Route::post('loans', [AnggotaLoanController::class, 'store'])->name('loans.store');
        Route::get('my-loans', [AnggotaLoanController::class, 'index'])->name('loans.index');
        Route::get('loans/{loan}', [AnggotaLoanController::class, 'show'])->name('loans.show');
        Route::post('loans/{loan}/cancel', [AnggotaLoanController::class, 'cancel'])->name('loans.cancel');
        Route::get('articles', [AnggotaArticleController::class, 'index'])->name('articles.index');
        Route::get('articles/{article:slug}', [AnggotaArticleController::class, 'show'])->name('articles.show');
        Route::get('essays', [AnggotaEssayController::class, 'index'])->name('essays.index');
        Route::get('essays/create', [AnggotaEssayController::class, 'create'])->name('essays.create');
        Route::post('essays', [AnggotaEssayController::class, 'store'])->name('essays.store');
        Route::get('essays/{essay}', [AnggotaEssayController::class, 'show'])->name('essays.show');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Public Storage Fallback Route (Shared Hosting Symlink Workaround)
Route::get('/storage/{path}', [StorageController::class, 'show'])
    ->where('path', '.*')
    ->name('storage.show');

