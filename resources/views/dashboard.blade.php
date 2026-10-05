<x-app-layout>
    <x-slot name="header">
        Ringkasan & Dasbor Pustaka
    </x-slot>

    <div class="space-y-10 animate-in fade-in duration-300">
        <!-- Editorial Welcome Header -->
        <div class="bg-white p-6 sm:p-8 rounded-lg border border-neutral-border shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-1.5">
                <span class="text-xs uppercase tracking-[0.2em] font-semibold text-primary block">
                    {{ Auth::user()->isAnggota() ? 'Portal Pembaca Anggota' : 'Pusat Kendali Administrator' }}
                </span>
                <h2 class="font-sans text-2xl sm:text-3xl font-bold text-neutral-dark tracking-tight">
                    Selamat Datang, {{ Auth::user()->name }}
                </h2>
                <p class="text-xs sm:text-sm text-neutral-body">
                    {{ Auth::user()->isAnggota() 
                        ? 'Kelola koleksi pinjaman aktif, nikmati bacaan digital PDF, telusuri buku fisik di rak, dan kontribusikan tulisan esai Anda.' 
                        : 'Pantau kelancaran sirkulasi buku fisik & digital, ketersediaan eksemplar, kepatuhan pengembalian, dan kurasi karya esai anggota.' }}
                </p>
            </div>

            <div class="shrink-0 flex items-center gap-3">
                @if(Auth::user()->isAnggota())
                    <a href="{{ route('anggota.books.index') }}" class="btn-editorial text-xs py-2.5 px-5 uppercase tracking-wider font-semibold">
                        Jelajahi Katalog &rarr;
                    </a>
                @else
                    <a href="{{ route('admin.loans.create') }}" class="btn-editorial text-xs py-2.5 px-5 uppercase tracking-wider font-semibold">
                        + Catat Pinjaman
                    </a>
                @endif
            </div>
        </div>

        <!-- Role-based Circulation Metrics -->
        @if(Auth::user()->isAdmin())
            <!-- Admin 6-Column Hybrid Metrics Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs">
                    <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block">Stok Fisik</span>
                    <span class="font-sans text-2xl font-bold text-neutral-dark block leading-tight mt-1">
                        {{ number_format($stats['total_physical_stock'] ?? $stats['total_books']) }}
                    </span>
                    <span class="text-[10px] text-neutral-muted">Eksemplar di rak</span>
                </div>

                <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs">
                    <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block">Buku Digital</span>
                    <span class="font-sans text-2xl font-bold text-accent block leading-tight mt-1">
                        {{ number_format($stats['total_digital_books'] ?? 0) }}
                    </span>
                    <span class="text-[10px] text-neutral-muted">Tersedia e-book</span>
                </div>

                <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs">
                    <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block">Anggota</span>
                    <span class="font-sans text-2xl font-bold text-neutral-dark block leading-tight mt-1">
                        {{ number_format($stats['total_members']) }}
                    </span>
                    <span class="text-[10px] text-neutral-muted">Terdaftar aktif</span>
                </div>

                <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs">
                    <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block">Pinjaman Fisik</span>
                    <span class="font-sans text-2xl font-bold text-primary block leading-tight mt-1">
                        {{ number_format($stats['active_physical_loans'] ?? $stats['active_loans']) }}
                    </span>
                    <span class="text-[10px] text-neutral-muted">Sirkulasi berjalan</span>
                </div>

                <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs">
                    <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block">Akses Digital</span>
                    <span class="font-sans text-2xl font-bold text-green-700 block leading-tight mt-1">
                        {{ number_format($stats['active_digital_loans'] ?? 0) }}
                    </span>
                    <span class="text-[10px] text-neutral-muted">Sedang dipinjam</span>
                </div>

                <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs">
                    <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block">Kurasi Esai</span>
                    <span class="font-sans text-2xl font-bold text-amber-700 block leading-tight mt-1">
                        {{ number_format($stats['pending_essays'] ?? 0) }}
                    </span>
                    <span class="text-[10px] text-neutral-muted">Menunggu review</span>
                </div>
            </div>
        @else
            <!-- Anggota 4-Column Reading Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-lg border border-neutral-border shadow-xs">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-md bg-primary-light text-primary flex items-center justify-center shrink-0 border border-red-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block">Pinjaman Fisik</span>
                            <span class="font-sans text-2xl font-bold text-neutral-dark block leading-tight">{{ $stats['my_borrowed'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-lg border border-neutral-border shadow-xs">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-md bg-amber-50 text-accent flex items-center justify-center shrink-0 border border-amber-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block">Akses Digital</span>
                            <span class="font-sans text-2xl font-bold text-neutral-dark block leading-tight">{{ $stats['my_digital_active'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-lg border border-neutral-border shadow-xs">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-md bg-[#F8F8F7] text-neutral-dark flex items-center justify-center shrink-0 border border-neutral-border">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block">Total Riwayat</span>
                            <span class="font-sans text-2xl font-bold text-neutral-dark block leading-tight">{{ $stats['my_loans'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-lg border border-neutral-border shadow-xs">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-md bg-purple-50 text-purple-700 flex items-center justify-center shrink-0 border border-purple-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block">Esai Saya</span>
                            <span class="font-sans text-2xl font-bold text-neutral-dark block leading-tight">{{ $stats['my_essays'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Content Area: 2-Column Split -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left 8-cols: Activities / Recent Additions -->
            <div class="lg:col-span-8 space-y-4">
                <div class="flex items-baseline justify-between px-1">
                    <div>
                        <h3 class="font-sans text-lg sm:text-xl font-bold text-neutral-dark">
                            {{ Auth::user()->isAnggota() ? 'Koleksi Terbaru untuk Ditelaah' : 'Log Sirkulasi Terkini' }}
                        </h3>
                        <p class="text-xs text-neutral-muted mt-0.5">
                            {{ Auth::user()->isAnggota() ? 'Naskah rujukan dan bacaan yang baru saja diindeks ke dalam perpustakaan.' : 'Catatan transaksi peminjaman dan pengembalian buku fisik & digital terbaru.' }}
                        </p>
                    </div>

                    <a href="{{ Auth::user()->isAnggota() ? route('anggota.books.index') : route('admin.loans.index') }}" 
                       class="text-xs font-semibold text-primary hover:underline uppercase tracking-wider">
                        Lihat Seluruhnya &rarr;
                    </a>
                </div>

                @if(Auth::user()->isAnggota())
                    <!-- Member View: Curated Books List -->
                    <div class="bg-white rounded-lg border border-neutral-border divide-y divide-neutral-border shadow-xs">
                        @forelse($activities as $book)
                            <div class="p-4 sm:p-5 flex items-center justify-between gap-4 hover:bg-[#F8F8F7] transition-colors">
                                <div class="flex items-center space-x-4 min-w-0">
                                    <div class="w-12 h-16 bg-[#F8F8F7] rounded overflow-hidden shrink-0 border border-neutral-border">
                                        @if($book->cover_url)
                                            <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-primary">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-semibold text-primary uppercase tracking-wider">{{ $book->category->name ?? 'Umum' }}</span>
                                            @if($book->hasDigital())
                                                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">PDF</span>
                                            @endif
                                        </div>
                                        <h4 class="font-sans text-sm font-semibold text-neutral-dark truncate hover:text-primary transition-colors mt-0.5">
                                            <a href="{{ route('anggota.books.show', $book) }}">{{ $book->title }}</a>
                                        </h4>
                                        <p class="text-xs text-neutral-muted mt-0.5 truncate">{{ $book->author }}</p>
                                    </div>
                                </div>

                                <div class="shrink-0 flex items-center space-x-3">
                                    <span class="hidden sm:inline-block px-2.5 py-0.5 text-[10px] font-bold rounded {{ $book->available_stock > 0 ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-neutral-100 text-neutral-600 border border-neutral-border' }}">
                                        {{ $book->available_stock > 0 ? 'Fisik: '.$book->available_stock : 'Hanya Digital' }}
                                    </span>
                                    <a href="{{ route('anggota.books.show', $book) }}" class="btn-editorial text-xs py-1.5 px-3">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-xs text-neutral-muted italic">
                                Belum ada buku terbaru yang diindeks.
                            </div>
                        @endforelse
                    </div>
                @else
                    <!-- Admin View: Circulation Activity Table -->
                    <x-table :headers="['Anggota Peminjam', 'Kode & Buku', 'Jenis', 'Status', 'Waktu']">
                        @forelse($activities as $activity)
                            <tr class="hover:bg-[#F8F8F7] transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-md bg-primary-light text-primary font-sans font-bold text-xs flex items-center justify-center border border-red-200">
                                            {{ substr($activity->user->name ?? 'A', 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="text-xs font-semibold text-neutral-dark block leading-tight">{{ $activity->user->name ?? 'Anggota' }}</span>
                                            <span class="text-[10px] text-neutral-muted block">{{ $activity->user->email ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-mono text-[10px] text-primary font-bold block">{{ $activity->loan_code }}</span>
                                    <span class="text-xs font-medium text-neutral-dark truncate max-w-[200px] block">
                                        {{ $activity->loanDetails->first()->book->title ?? 'Buku Perpustakaan' }}
                                        @if($activity->loanDetails->count() > 1)
                                            <span class="text-accent text-[10px] font-semibold">(+{{ $activity->loanDetails->count() - 1 }})</span>
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($activity->isDigital())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                            Digital
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-50 text-slate-700 border border-neutral-border">
                                            Fisik
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($activity->status === 'borrowed')
                                        <x-badge variant="primary">Dipinjam</x-badge>
                                    @elseif($activity->status === 'returned')
                                        <x-badge variant="emerald">Kembali</x-badge>
                                    @else
                                        <x-badge variant="rose">Terlambat</x-badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-neutral-muted italic">
                                    {{ $activity->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-xs text-neutral-muted italic">
                                    Belum ada log sirkulasi tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </x-table>
                @endif
            </div>

            <!-- Right 4-cols: User Profile & Quick Guidelines -->
            <div class="lg:col-span-4 space-y-6">
                <!-- User Profile Ledger Card -->
                <div class="bg-white rounded-lg border border-neutral-border p-6 shadow-xs space-y-6">
                    <div class="text-center pb-5 border-b border-neutral-border">
                        <div class="w-16 h-16 rounded-md bg-primary-light text-primary border border-red-200 font-sans text-2xl font-bold mx-auto flex items-center justify-center mb-3 shadow-xs">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <h4 class="font-sans text-lg font-bold text-neutral-dark">{{ Auth::user()->name }}</h4>
                        <span class="inline-block mt-1 px-2.5 py-0.5 bg-primary-light text-primary text-[10px] font-bold uppercase tracking-wider rounded border border-red-200">
                            {{ Auth::user()->isAdmin() ? 'Administrator' : 'Anggota Perpustakaan' }}
                        </span>
                    </div>

                    <div class="space-y-3 text-xs text-neutral-body">
                        <div class="flex justify-between items-center py-1 border-b border-neutral-border">
                            <span class="text-neutral-muted">Alamat Email</span>
                            <span class="font-medium text-neutral-dark truncate max-w-[170px]">{{ Auth::user()->email }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-neutral-border">
                            <span class="text-neutral-muted">Terdaftar Sejak</span>
                            <span class="font-medium text-neutral-dark">{{ Auth::user()->created_at->format('M Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-neutral-muted">Status Akun</span>
                            <x-badge variant="emerald">{{ Auth::user()->isAdmin() ? 'Otoritas Penuh' : 'Aktif / Terverifikasi' }}</x-badge>
                        </div>
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('profile.edit') }}" class="btn-editorial-outline w-full py-2.5 text-xs uppercase tracking-wider font-semibold justify-center">
                            Perbarui Profil Akun
                        </a>
                    </div>
                </div>

                <!-- Academic Guidance Box with subtle gold star -->
                <div class="bg-[#F8F8F7] rounded-lg border border-neutral-border p-6 space-y-3">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-accent fill-current" viewBox="0 0 24 24">
                            <path d="M12 2l2.4 7.2h7.6l-6.2 4.5 2.4 7.3-6.2-4.6-6.2 4.6 2.4-7.3-6.2-4.5h7.6z"/>
                        </svg>
                        <span class="text-[10px] font-bold text-primary uppercase tracking-widest block">Pedoman Pembaca</span>
                    </div>
                    <h5 class="font-sans text-base font-bold text-neutral-dark">Ketepatan Sirkulasi</h5>
                    <p class="text-xs text-neutral-body leading-relaxed">
                        Pastikan setiap peminjaman fisik dikembalikan sebelum tanggal jatuh tempo. Untuk peminjaman digital, masa akses otomatis ditutup setelah 7 hari.
                    </p>
                    <div class="pt-2 text-[11px] text-neutral-muted italic">
                        Bantuan: hubungi administrator pustaka melalui layanan meja sirkulasi.
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
