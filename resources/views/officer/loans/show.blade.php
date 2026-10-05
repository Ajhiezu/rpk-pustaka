<x-app-layout>
    <x-slot name="header">
        Detail Sirkulasi Peminjaman & Reservasi
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('admin.loans.index') }}" class="btn-editorial-outline text-xs py-2 px-3.5 uppercase tracking-wider inline-flex items-center">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Sirkulasi
        </a>
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300">
        <!-- Identity & Status Banner Card -->
        <div class="bg-white p-5 rounded-lg border border-neutral-border shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="space-y-1.5">
                <nav class="flex items-center gap-2 text-[11px] text-neutral-muted uppercase tracking-wider font-mono">
                    <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dasbor</a>
                    <span>/</span>
                    <a href="{{ route('admin.loans.index') }}" class="hover:text-primary transition-colors">Sirkulasi</a>
                    <span>/</span>
                    <span class="text-primary font-bold">{{ $loan->loan_code }}</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-xl sm:text-2xl font-bold text-neutral-dark tracking-tight leading-snug">
                        Sirkulasi Pinjaman <span class="text-neutral-muted font-mono text-lg font-normal">#{{ $loan->loan_code }}</span>
                    </h2>
                    
                    @if($loan->isPending())
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold bg-amber-50 text-amber-900 border border-amber-300 rounded">
                            🔒 Menunggu Persetujuan (Pending)
                        </span>
                    @elseif($loan->isApproved())
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold bg-blue-50 text-blue-900 border border-blue-300 rounded">
                            ✓ Disetujui (Menunggu Pengambilan)
                        </span>
                    @elseif($loan->isBorrowed())
                        @if($loan->isExpired() || \Carbon\Carbon::parse($loan->due_date)->isPast())
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold bg-red-50 text-danger border border-red-200 rounded">
                                Terlambat Pengembalian
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold bg-emerald-50 text-emerald-900 border border-emerald-300 rounded">
                                Aktif Dipinjam
                            </span>
                        @endif
                    @elseif($loan->isReturned())
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold bg-[#EDF7ED] text-success border border-[#C8E6C9] rounded">
                            Selesai Dikembalikan
                        </span>
                    @elseif($loan->isCancelled())
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold bg-red-50 text-red-800 border border-red-200 rounded">
                            ✕ Dibatalkan Anggota
                        </span>
                    @elseif($loan->isRejected())
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold bg-red-50 text-red-800 border border-red-200 rounded">
                            ✕ Ditolak Petugas
                        </span>
                    @elseif($loan->isExpiredState())
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold bg-gray-100 text-gray-800 border border-gray-300 rounded">
                            ⏱️ Kedaluwarsa
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold bg-red-50 text-danger border border-red-200 rounded">
                            {{ ucfirst($loan->status) }}
                        </span>
                    @endif

                    @if($loan->isDigital())
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold bg-[#FFF9ED] text-[#B45309] border border-[#FDE68A] rounded">
                            Koleksi Digital
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold bg-[#F8F8F7] text-neutral-dark border border-neutral-border rounded">
                            Koleksi Fisik
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4 text-xs font-mono text-neutral-muted pt-3 lg:pt-0 border-t lg:border-t-0 border-neutral-border">
                <div>
                    <span class="text-neutral-muted block text-[10px] uppercase tracking-wider">Tanggal Pengajuan</span>
                    <span class="font-bold text-neutral-dark">{{ \Carbon\Carbon::parse($loan->created_at)->format('d M Y H:i') }}</span>
                </div>
                <div class="w-px h-6 bg-neutral-border hidden sm:block"></div>
                @if($loan->pickup_deadline && ($loan->isPending() || $loan->isApproved()))
                    <div>
                        <span class="text-red-600 block text-[10px] uppercase tracking-wider font-bold">Batas Pengambilan</span>
                        <span class="font-bold text-primary">
                            {{ $loan->pickup_deadline->format('d M Y, H:i') }}
                        </span>
                    </div>
                @else
                    <div>
                        <span class="text-neutral-muted block text-[10px] uppercase tracking-wider">Batas Tenggat</span>
                        <span class="font-bold {{ \Carbon\Carbon::parse($loan->due_date)->isPast() && $loan->status === 'borrowed' ? 'text-danger' : 'text-neutral-dark' }}">
                            {{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}
                        </span>
                    </div>
                @endif
                <div class="w-px h-6 bg-neutral-border hidden sm:block"></div>
                <div>
                    <span class="text-neutral-muted block text-[10px] uppercase tracking-wider">Total Koleksi</span>
                    <span class="font-bold text-neutral-dark">{{ count($loan->loanDetails) }} Eksemplar</span>
                </div>
            </div>
        </div>

        <!-- Admin Action Bar for Pending & Approved Statuses -->
        @if($loan->isPending())
            <div class="bg-amber-50 border-2 border-amber-300 p-5 rounded-lg shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4" x-data="{ showRejectModal: false }">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 block">AKSI ADMIN — VERIFIKASI RESERVASI PENDING</span>
                    <h3 class="font-sans text-base font-bold text-amber-950">Permohonan Reservasi Buku Fisik Membutuhkan Persetujuan Admin</h3>
                    <p class="text-xs text-amber-900">Stok fisik telah otomatis di-lock (berkurang 1 kuota) saat reservasi dibuat. Menyetujui tidak akan memotong stok lagi.</p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <!-- Approve Button -->
                    <form action="{{ route('admin.loans.approve', $loan) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded font-bold text-xs uppercase tracking-wider shadow-xs transition-colors flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Setujui Reservasi
                        </button>
                    </form>

                    <!-- Reject Button Trigger Modal -->
                    <button type="button" @click="showRejectModal = true" class="px-4 py-2.5 bg-red-100 hover:bg-red-200 text-red-800 border border-red-300 rounded font-bold text-xs uppercase tracking-wider transition-colors">
                        Tolak Reservasi
                    </button>
                </div>

                <!-- Reject Modal -->
                <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-black/50 p-4">
                    <div class="bg-white rounded-lg max-w-md w-full p-6 space-y-4 border border-neutral-border shadow-lg" @click.outside="showRejectModal = false">
                        <h4 class="font-sans text-base font-bold text-neutral-dark">Tolak Reservasi Peminjaman</h4>
                        <p class="text-xs text-neutral-body">Stok buku fisik akan dilepas kembali ke rak perpustakaan. Masukkan alasan penolakan untuk anggota:</p>

                        <form action="{{ route('admin.loans.reject', $loan) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-neutral-dark uppercase tracking-wider mb-1">Alasan Penolakan</label>
                                <textarea name="rejection_reason" rows="3" required class="w-full text-xs p-3 border border-neutral-border rounded focus:ring-primary focus:border-primary" placeholder="Contoh: Buku sedang dalam proses perawatan fisik / inventarisasi."></textarea>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2">
                                <button type="button" @click="showRejectModal = false" class="px-4 py-2 text-xs font-semibold text-neutral-body bg-neutral-100 rounded hover:bg-neutral-200">
                                    Batal
                                </button>
                                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-red-700 hover:bg-red-800 rounded uppercase tracking-wider">
                                    Konfirmasi Penolakan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @elseif($loan->isApproved())
            <div class="bg-blue-50 border-2 border-blue-300 p-5 rounded-lg shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-800 block">AKSI ADMIN — SERAH TERIMA BUKU FISIK</span>
                    <h3 class="font-sans text-base font-bold text-blue-950">Reservasi Disetujui. Anggota Datang Mengambil Buku</h3>
                    <p class="text-xs text-blue-900">Verifikasi Kode Bukti: <strong class="font-mono text-primary">{{ $loan->loan_code }}</strong>. Klik tombol saat fisik buku diserahkan kepada Anggota.</p>
                </div>

                <form action="{{ route('admin.loans.handover', $loan) }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-blue-700 hover:bg-blue-800 text-white rounded font-bold text-xs uppercase tracking-wider shadow-md transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tandai Buku Diserahkan (Handover)
                    </button>
                </form>
            </div>
        @endif

        <!-- 2 Column Main Grid -->
        <div class="grid lg:grid-cols-3 gap-6" x-data="{ condition: 'good' }">
            
            <!-- Left Column (2 Cols) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Loaned Books Card -->
                <div class="bg-white border border-neutral-border rounded-lg overflow-hidden shadow-xs">
                    <div class="px-6 py-4 border-b border-neutral-border bg-[#F8F8F7] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                            <h3 class="text-xs font-bold text-neutral-dark uppercase tracking-wider">
                                Koleksi Buku Terpinjam / Reservasi ({{ count($loan->loanDetails) }})
                            </h3>
                        </div>
                        <span class="text-xs text-neutral-muted font-mono">Kode: {{ $loan->loan_code }}</span>
                    </div>

                    <div class="divide-y divide-neutral-border">
                        @foreach($loan->loanDetails as $detail)
                        <div class="p-6 flex flex-col sm:flex-row items-start gap-5 hover:bg-[#F8F8F7]/50 transition-colors">
                            <div class="w-20 h-28 sm:w-24 sm:h-32 bg-[#F8F8F7] border border-neutral-border overflow-hidden flex-shrink-0 flex items-center justify-center shadow-xs rounded">
                                @if($detail->book && $detail->book->image)
                                    <img src="{{ asset('storage/'.$detail->book->image) }}" alt="{{ $detail->book->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="text-center p-2">
                                        <svg class="w-8 h-8 text-neutral-muted mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <span class="text-[9px] text-neutral-muted uppercase tracking-wider font-mono">No Cover</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <span class="text-[10px] font-mono uppercase tracking-wider text-primary font-bold bg-primary-light px-2 py-0.5 rounded border border-red-100">
                                        {{ $detail->book->category->name ?? 'Koleksi Umum' }}
                                    </span>
                                    @if($detail->book && $detail->book->location)
                                        <span class="text-[10px] font-mono text-neutral-body bg-[#F8F8F7] px-2 py-0.5 rounded border border-neutral-border">
                                            Rak: {{ $detail->book->location->name }}
                                        </span>
                                    @endif
                                    @if($detail->book && $detail->book->book_code)
                                        <span class="text-[10px] font-mono text-neutral-muted">
                                            Kode: {{ $detail->book->book_code }}
                                        </span>
                                    @endif
                                </div>

                                <h4 class="text-base sm:text-lg font-bold text-neutral-dark mb-1 leading-snug">
                                    {{ $detail->book->title ?? 'Judul Buku Tidak Ditemukan' }}
                                </h4>

                                <p class="text-xs text-neutral-body mb-3">
                                    Penulis: <span class="font-medium text-neutral-dark">{{ $detail->book->author ?? '-' }}</span> 
                                    @if($detail->book && $detail->book->year)
                                        • Tahun Terbit: <span class="font-medium text-neutral-dark">{{ $detail->book->year }}</span>
                                    @endif
                                </p>

                                <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-neutral-border text-xs">
                                    <div class="flex flex-wrap items-center gap-4 text-neutral-muted font-mono text-[11px]">
                                        <span>ISBN: <strong class="text-neutral-dark font-mono">{{ $detail->book->isbn ?? '-' }}</strong></span>
                                        <span>Penerbit: <strong class="text-neutral-dark">{{ $detail->book->publisher ?? '-' }}</strong></span>
                                        @if($detail->book && $detail->book->price)
                                            <span>Nilai Buku: <strong class="text-neutral-dark">Rp {{ number_format($detail->book->price, 0, ',', '.') }}</strong></span>
                                        @endif
                                    </div>

                                    @if($detail->book)
                                        <a href="{{ route('admin.books.show', $detail->book) }}" class="text-primary hover:text-primary-dark font-medium text-xs inline-flex items-center gap-1 group">
                                            <span>Detail Koleksi</span>
                                            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                @if($loan->isBorrowed() || $loan->isOverdue())
                <!-- Return Processing Form Card -->
                <div class="bg-white border border-neutral-border rounded-lg overflow-hidden shadow-xs">
                    <div class="px-6 py-4 border-b border-neutral-border bg-[#F8F8F7] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                            <h3 class="text-xs font-bold text-neutral-dark uppercase tracking-wider">
                                {{ $loan->isDigital() ? 'Formulir Pengembalian Naskah Digital' : 'Formulir Pengembalian Koleksi Fisik' }}
                            </h3>
                        </div>
                        <span class="text-xs font-mono font-semibold text-neutral-muted">
                            {{ $loan->isDigital() ? 'Sistem Sirkulasi Digital' : 'Verifikasi Fisik & Integritas' }}
                        </span>
                    </div>

                    <form action="{{ route('admin.loans.returnBook', $loan) }}" method="POST" class="p-6 sm:p-8 space-y-6">
                        @csrf

                        @if($loan->isDigital())
                            <input type="hidden" name="condition" value="good">
                            <div class="p-4 rounded-lg bg-[#FFF9ED] border border-[#FDE68A] text-neutral-dark text-xs leading-relaxed flex items-start gap-3 shadow-xs">
                                <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-[#B45309] shrink-0 border border-[#FDE68A]">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <span class="font-bold text-[#B45309] block text-sm mb-0.5">Peminjaman Naskah Digital</span>
                                    <span>Pengembalian naskah digital dicatat secara otomatis oleh sistem tanpa verifikasi kondisi fisik.</span>
                                </div>
                            </div>
                        @else
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <label class="block text-xs font-bold text-neutral-dark uppercase tracking-wider">
                                        Kondisi Fisik Koleksi Saat Kembali <span class="text-primary">*</span>
                                    </label>
                                    <span class="text-[11px] font-mono text-neutral-muted">Pilih 1 dari 3 status</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <!-- Option 1: Baik -->
                                    <label @click="condition = 'good'"
                                           :class="condition === 'good' 
                                               ? 'border-emerald-600 bg-emerald-50/40 ring-1 ring-emerald-600 shadow-xs' 
                                               : 'border-neutral-border bg-white hover:border-neutral-300 hover:bg-[#F8F8F7]'"
                                           class="relative flex flex-col justify-between p-4.5 rounded-lg border-2 transition-all cursor-pointer select-none">
                                        <input type="radio" name="condition" value="good" x-model="condition" class="sr-only">
                                        
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="font-sans font-bold text-sm text-neutral-dark">
                                                    Kondisi Baik
                                                </span>
                                                <div :class="condition === 'good' ? 'border-emerald-600 bg-emerald-600' : 'border-neutral-300 bg-white'"
                                                     class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors">
                                                    <div x-show="condition === 'good'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                                </div>
                                            </div>
                                            <p class="text-xs text-neutral-body leading-relaxed">
                                                Naskah utuh, bersih, dan tanpa cacat fisik atau coretan.
                                            </p>
                                        </div>

                                        <div class="mt-4 pt-3 border-t border-neutral-border/70 flex items-center gap-1.5 text-xs">
                                            <span class="inline-flex items-center gap-1.5 text-emerald-700 font-semibold text-[11px]">
                                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                +1 Stok Rak Tersedia
                                            </span>
                                        </div>
                                    </label>

                                    <!-- Option 2: Rusak -->
                                    <label @click="condition = 'damaged'"
                                           :class="condition === 'damaged' 
                                               ? 'border-accent bg-amber-50/60 ring-1 ring-accent shadow-xs' 
                                               : 'border-neutral-border bg-white hover:border-neutral-300 hover:bg-[#F8F8F7]'"
                                           class="relative flex flex-col justify-between p-4.5 rounded-lg border-2 transition-all cursor-pointer select-none">
                                        <input type="radio" name="condition" value="damaged" x-model="condition" class="sr-only">
                                        
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="font-sans font-bold text-sm text-neutral-dark">
                                                    Buku Rusak
                                                </span>
                                                <div :class="condition === 'damaged' ? 'border-accent bg-accent' : 'border-neutral-300 bg-white'"
                                                     class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors">
                                                    <div x-show="condition === 'damaged'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                                </div>
                                            </div>
                                            <p class="text-xs text-neutral-body leading-relaxed">
                                                Terdapat robekan, halaman lepas, basah, atau noda parah.
                                            </p>
                                        </div>

                                        <div class="mt-4 pt-3 border-t border-neutral-border/70 flex items-center gap-1.5 text-xs">
                                            <span class="inline-flex items-center gap-1.5 text-amber-800 font-semibold text-[11px]">
                                                Ganti Rugi 100% Harga Buku
                                            </span>
                                        </div>
                                    </label>

                                    <!-- Option 3: Hilang -->
                                    <label @click="condition = 'lost'"
                                           :class="condition === 'lost' 
                                               ? 'border-primary bg-primary-light/60 ring-1 ring-primary shadow-xs' 
                                               : 'border-neutral-border bg-white hover:border-neutral-300 hover:bg-[#F8F8F7]'"
                                           class="relative flex flex-col justify-between p-4.5 rounded-lg border-2 transition-all cursor-pointer select-none">
                                        <input type="radio" name="condition" value="lost" x-model="condition" class="sr-only">
                                        
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="font-sans font-bold text-sm text-neutral-dark">
                                                    Buku Hilang
                                                </span>
                                                <div :class="condition === 'lost' ? 'border-primary bg-primary' : 'border-neutral-300 bg-white'"
                                                     class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors">
                                                    <div x-show="condition === 'lost'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                                </div>
                                            </div>
                                            <p class="text-xs text-neutral-body leading-relaxed">
                                                Koleksi fisik tidak dapat dikembalikan oleh peminjam.
                                            </p>
                                        </div>

                                        <div class="mt-4 pt-3 border-t border-neutral-border/70 flex items-center gap-1.5 text-xs">
                                            <span class="inline-flex items-center gap-1.5 text-primary font-semibold text-[11px]">
                                                Ganti Rugi 100% Harga Buku
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label for="notes" class="block text-xs font-bold text-neutral-dark uppercase tracking-wider mb-2">
                                {{ $loan->isDigital() ? 'Catatan Pengembalian (Opsional)' : 'Catatan Pemeriksaan Fisik (Opsional)' }}
                            </label>
                            <textarea id="notes" name="notes" rows="3" class="w-full bg-[#F8F8F7] border border-neutral-border text-neutral-dark text-sm p-3 rounded-md focus:outline-none focus:border-primary focus:bg-white transition-colors" placeholder="Catat keteragan pengembalian..."></textarea>
                        </div>

                        <div class="pt-4 border-t border-neutral-border flex flex-col sm:flex-row items-center justify-between gap-4">
                            <p class="text-xs text-neutral-muted">
                                Konfirmasi ini akan memperbarui status sirkulasi perpustakaan.
                            </p>
                            <button type="submit" class="btn-editorial w-full sm:w-auto px-8 py-2.5 text-xs tracking-wider uppercase font-semibold">
                                {{ $loan->isDigital() ? 'Konfirmasi Pengembalian Digital' : 'Konfirmasi Pengembalian Fisik' }}
                            </button>
                        </div>
                    </form>
                </div>
                @elseif($loan->isPending())
                <!-- Detail Info if Pending -->
                <div class="bg-amber-50 border-2 border-amber-300 p-6 rounded-lg shadow-xs space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <h3 class="text-sm font-bold text-amber-950 uppercase tracking-wider">Status Transaksi: {{ $loan->status_label }}</h3>
                    </div>
                    <p class="text-xs text-amber-900 leading-relaxed">
                        🔒 Stok fisik buku ini telah dikunci (reserved) secara otomatis untuk peminjam. Menunggu persetujuan Admin atau serah terima di perpustakaan.
                    </p>
                </div>
                @elseif($loan->isApproved())
                <!-- Detail Info if Approved -->
                <div class="bg-blue-50 border-2 border-blue-300 p-6 rounded-lg shadow-xs space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                        <h3 class="text-sm font-bold text-blue-950 uppercase tracking-wider">Status Transaksi: {{ $loan->status_label }}</h3>
                    </div>
                    <p class="text-xs text-blue-900 leading-relaxed">
                        ✓ Reservasi telah disetujui Admin. Buku fisik siap diserahkan saat Anggota datang ke perpustakaan.
                    </p>
                </div>
                @elseif($loan->isReturned())
                <!-- Detail Info if Returned -->
                <div class="bg-white border border-neutral-border p-6 rounded-lg shadow-xs space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                        <h3 class="text-sm font-bold text-neutral-dark uppercase tracking-wider">Status Transaksi: Dikembalikan</h3>
                    </div>
                    <p class="text-xs text-neutral-body">Buku telah resmi dikembalikan ke perpustakaan. Stok fisik telah dipulihkan kembali ke rak.</p>
                </div>
                @else
                <!-- Detail Info if Rejected/Cancelled/Expired -->
                <div class="bg-white border border-neutral-border p-6 rounded-lg shadow-xs space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span>
                        <h3 class="text-sm font-bold text-neutral-dark uppercase tracking-wider">Status Transaksi: {{ $loan->status_label }}</h3>
                    </div>
                    <p class="text-xs text-neutral-body">
                        @if($loan->isCancelled())
                            Reservasi dibatalkan oleh Anggota. Stok fisik telah dilepas kembali ke perpustakaan.
                        @elseif($loan->isRejected())
                            Reservasi ditolak oleh Petugas. @if($loan->rejection_reason) Alasan: <strong>{{ $loan->rejection_reason }}</strong> @endif
                        @elseif($loan->isExpiredState())
                            Reservasi kedaluwarsa karena batas waktu pengambilan (pickup deadline) telah terlewati. Stok fisik telah dilepas kembali.
                        @else
                            Status transaksi: {{ $loan->status_label }}.
                        @endif
                    </p>
                </div>
                @endif
            </div>

            <!-- Right Sidebar Column (1 Col) -->
            <div class="space-y-6">
                <!-- Member Information Card -->
                <div class="bg-white border border-neutral-border rounded-lg overflow-hidden shadow-xs">
                    <div class="px-6 py-4 border-b border-neutral-border bg-[#F8F8F7] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                            <h3 class="text-xs font-bold text-neutral-dark uppercase tracking-wider">
                                Identitas Peminjam
                            </h3>
                        </div>
                        <span class="text-xs font-mono text-neutral-muted">Anggota</span>
                    </div>

                    <div class="p-6">
                        <div class="flex items-center gap-4 pb-5 border-b border-neutral-border">
                            <div class="w-12 h-12 bg-primary-light border border-red-200 text-primary font-bold text-base flex items-center justify-center rounded-full flex-shrink-0">
                                {{ strtoupper(substr($loan->user->name ?? 'A', 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-bold text-base text-neutral-dark truncate">{{ $loan->user->name ?? 'Anggota' }}</h4>
                                <p class="text-xs text-neutral-muted font-mono truncate">{{ $loan->user->email ?? '-' }}</p>
                            </div>
                        </div>

                        <dl class="mt-4 space-y-3 text-xs">
                            <div class="flex justify-between py-1 border-b border-neutral-border/60">
                                <dt class="text-neutral-muted">Role Anggota</dt>
                                <dd class="font-medium text-neutral-dark capitalize">{{ $loan->user->role ?? 'Anggota' }}</dd>
                            </div>
                            <div class="flex justify-between py-1 border-b border-neutral-border/60">
                                <dt class="text-neutral-muted">Nomor Kontak / WA</dt>
                                <dd class="font-mono text-neutral-dark font-medium">{{ $loan->user->phone ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Circulation Timeline Card -->
                <div class="bg-white border border-neutral-border rounded-lg overflow-hidden shadow-xs">
                    <div class="px-6 py-4 border-b border-neutral-border bg-[#F8F8F7] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                            <h3 class="text-xs font-bold text-neutral-dark uppercase tracking-wider">
                                Timeline & Deadlines
                            </h3>
                        </div>
                        <span class="text-xs font-mono text-neutral-muted">Timeline</span>
                    </div>

                    <div class="p-6 space-y-4 text-xs">
                        <div class="flex items-center justify-between py-1.5 border-b border-neutral-border">
                            <span class="text-neutral-muted">Pengajuan Reservasi</span>
                            <span class="font-mono text-neutral-dark font-bold">
                                {{ \Carbon\Carbon::parse($loan->created_at)->format('d M Y H:i') }}
                            </span>
                        </div>

                        @if($loan->pickup_deadline)
                            <div class="flex items-center justify-between py-1.5 border-b border-neutral-border">
                                <span class="text-red-600 font-bold">Batas Pengambilan</span>
                                <span class="font-mono text-primary font-bold">
                                    {{ $loan->pickup_deadline->format('d M Y H:i') }}
                                </span>
                            </div>
                        @endif

                        @if($loan->approved_at)
                            <div class="flex items-center justify-between py-1.5 border-b border-neutral-border">
                                <span class="text-neutral-muted">Disetujui Admin</span>
                                <span class="font-mono text-blue-700 font-bold">
                                    {{ \Carbon\Carbon::parse($loan->approved_at)->format('d M Y H:i') }}
                                </span>
                            </div>
                        @endif

                        @if($loan->borrowed_at)
                            <div class="flex items-center justify-between py-1.5 border-b border-neutral-border">
                                <span class="text-neutral-muted">Penyerahan Buku</span>
                                <span class="font-mono text-emerald-700 font-bold">
                                    {{ \Carbon\Carbon::parse($loan->borrowed_at)->format('d M Y H:i') }}
                                </span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between py-1.5 border-b border-neutral-border">
                            <span class="text-neutral-muted">Tenggat Due Date</span>
                            <span class="font-mono text-neutral-dark font-bold">
                                {{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Fine & Financial Summary Card -->
                <div class="bg-white border border-neutral-border rounded-lg overflow-hidden shadow-xs">
                    <div class="px-6 py-4 border-b border-neutral-border bg-[#F8F8F7] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                            <h3 class="text-xs font-bold text-neutral-dark uppercase tracking-wider">
                                Rekapitulasi Denda & Biaya
                            </h3>
                        </div>
                        @if($loan->fine && $loan->fine->amount > 0)
                            <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded {{ ($loan->fine->status ?? '') === 'paid' ? 'bg-[#EDF7ED] text-success border border-[#C8E6C9]' : 'bg-red-50 text-danger border border-red-200' }}">
                                {{ ($loan->fine->status ?? '') === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                            </span>
                        @else
                            <template x-if="condition === 'good'">
                                <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-[#EDF7ED] text-success border border-[#C8E6C9]">
                                    Bebas Denda
                                </span>
                            </template>
                            <template x-if="condition !== 'good'">
                                <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-red-50 text-danger border border-red-200 font-bold">
                                    Estimasi Denda
                                </span>
                            </template>
                        @endif
                    </div>

                    <div class="p-6">
                        <p class="text-[10px] font-mono text-neutral-muted uppercase tracking-wider mb-1">
                            Total Kewajiban Denda
                        </p>

                        @if($loan->fine && $loan->fine->amount > 0)
                            <p class="text-2xl font-sans font-bold text-danger">
                                Rp {{ number_format($loan->fine->amount, 0, ',', '.') }}
                            </p>
                        @else
                            <p class="text-2xl font-sans font-bold transition-colors"
                               :class="condition === 'good' ? 'text-neutral-dark' : 'text-danger'">
                                <span x-show="condition === 'good'">Rp 0</span>
                                <span x-show="condition !== 'good'" x-cloak>Rp {{ number_format($estimatedFine ?? 0, 0, ',', '.') }}</span>
                            </p>
                        @endif

                        @if($loan->fine && $loan->fine->amount > 0)
                            <div class="mt-4 pt-3 border-t border-neutral-border space-y-2 text-xs">
                                <div class="flex justify-between py-1">
                                    <span class="text-neutral-muted">Jenis Denda</span>
                                    <span class="font-medium text-neutral-dark uppercase font-mono">
                                        @if($loan->fine->type === 'late')
                                            Keterlambatan
                                        @elseif($loan->fine->type === 'damaged')
                                            Kerusakan Fisik
                                        @elseif($loan->fine->type === 'lost')
                                            Penggantian Hilang
                                        @else
                                            {{ ucfirst($loan->fine->type) }}
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-neutral-muted">Status Pembayaran</span>
                                    <span class="font-bold {{ ($loan->fine->status ?? '') === 'paid' ? 'text-success' : 'text-danger' }}">
                                        {{ ($loan->fine->status ?? '') === 'paid' ? 'LUNAS' : 'BELUM DIBAYAR' }}
                                    </span>
                                </div>
                                @if($loan->fine->payment_date)
                                <div class="flex justify-between py-1">
                                    <span class="text-neutral-muted">Tanggal Pelunasan</span>
                                    <span class="font-mono text-success font-bold">{{ \Carbon\Carbon::parse($loan->fine->payment_date)->format('d M Y') }}</span>
                                </div>
                                @endif
                            </div>

                            @if(($loan->fine->status ?? '') === 'unpaid')
                                <!-- Form Pembayaran Denda -->
                                <div class="mt-5 pt-4 border-t border-neutral-border">
                                    <h4 class="text-xs font-bold text-neutral-dark uppercase tracking-wider mb-2.5">
                                        Formulir Penerimaan Denda
                                    </h4>
                                    <form action="{{ route('admin.loans.payFine', $loan) }}" method="POST" class="space-y-3">
                                        @csrf
                                        <div>
                                            <label class="block text-[10px] uppercase font-mono text-neutral-muted mb-1">
                                                Tanggal Pembayaran Diterima
                                            </label>
                                            <input type="date" name="payment_date" value="{{ now()->toDateString() }}" required
                                                   class="w-full px-3 py-2 bg-[#F8F8F7] border border-neutral-border rounded text-xs text-neutral-dark focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                                        </div>
                                        <button type="submit" onclick="return confirm('Konfirmasi penerimaan pembayaran denda sebesar Rp {{ number_format($loan->fine->amount, 0, ',', '.') }} dari {{ $loan->user->name }}?');" class="w-full py-2.5 px-4 bg-emerald-700 hover:bg-emerald-800 text-white rounded font-bold text-xs uppercase tracking-wider shadow-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Catat Lunas Pembayaran Denda
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @else
                            <p class="text-[11px] text-neutral-muted mt-2 italic">
                                Transaksi sirkulasi ini bebas dari beban denda keterlambatan maupun denda kerusakan fisik.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
