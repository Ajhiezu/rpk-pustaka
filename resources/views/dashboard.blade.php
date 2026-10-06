<x-app-layout>
    @php
        $digitalLoanDays = (int) \App\Models\Setting::get('digital_loan_duration_days', 7);
    @endphp

    <div class="space-y-8 sm:space-y-10 animate-in fade-in duration-300">
        <!-- Editorial Welcome Header (RPK Red #C62828 Theme) - TOP MOST ELEMENT -->
        <div class="bg-[#C62828] p-5 sm:p-6 md:p-8 rounded-xl shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-5 sm:gap-6 overflow-hidden relative">
            <!-- Subtle ambient bg circle -->
            <div class="absolute -right-12 -top-12 w-52 h-52 rounded-full bg-white/5 pointer-events-none"></div>
            <div class="absolute -left-8 -bottom-10 w-40 h-40 rounded-full bg-black/10 pointer-events-none"></div>

            <div class="space-y-1.5 sm:space-y-2 relative z-10 min-w-0">
                <div class="flex items-center gap-1.5">
                    <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-accent fill-current shrink-0" viewBox="0 0 24 24">
                        <path d="M12 2l2.4 7.2h7.6l-6.2 4.5 2.4 7.3-6.2-4.6-6.2 4.6 2.4-7.3-6.2-4.5h7.6z"/>
                    </svg>
                    <span class="text-[10px] sm:text-[11px] uppercase tracking-[0.18em] sm:tracking-[0.2em] font-bold text-white/80 block truncate">
                        {{ Auth::user()->isAnggota() ? 'Portal Pembaca Anggota' : 'Pusat Kendali Administrator' }}
                    </span>
                </div>
                <h2 class="font-sans text-xl sm:text-2xl md:text-3xl font-extrabold text-white tracking-tight leading-tight break-words">
                    {{ Auth::user()->isAnggota() ? 'Selamat Datang, '.Auth::user()->name : 'Selamat Datang, Administrator Pustaka' }}
                </h2>
                <p class="text-[11px] sm:text-xs md:text-sm text-white/80 leading-relaxed max-w-2xl">
                    {{ Auth::user()->isAnggota() 
                        ? 'Kelola koleksi pinjaman aktif, nikmati bacaan digital PDF, telusuri buku fisik di rak, dan kontribusikan tulisan esai Anda.' 
                        : 'Pantau kelancaran sirkulasi buku fisik & digital, ketersediaan eksemplar, kepatuhan pengembalian, dan kurasi karya esai anggota.' }}
                </p>
            </div>

            <div class="w-full sm:w-auto sm:shrink-0 flex items-center justify-start sm:justify-center gap-3 relative z-10">
                @if(Auth::user()->isAnggota())
                    <a href="{{ route('anggota.books.index') }}" class="inline-flex items-center justify-center gap-1.5 whitespace-normal sm:whitespace-nowrap text-center leading-snug px-5 sm:px-6 py-2.5 sm:py-3 bg-white text-primary hover:bg-neutral-surface font-bold text-[11px] sm:text-xs uppercase tracking-wider rounded-lg shadow-md transition-all hover:scale-[1.02] min-h-[44px]">
                        <span>Jelajahi Katalog</span>
                        <span class="shrink-0 font-sans">&rarr;</span>
                    </a>
                @else
                    <a href="{{ route('admin.loans.create') }}" class="inline-flex items-center justify-center gap-1.5 whitespace-normal sm:whitespace-nowrap text-center leading-snug px-5 sm:px-6 py-2.5 sm:py-3 bg-white text-primary hover:bg-neutral-surface font-bold text-[11px] sm:text-xs uppercase tracking-wider rounded-lg shadow-md transition-all hover:scale-[1.02] min-h-[44px]">
                        <span>+ Catat Pinjaman</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Role-based Circulation Metrics -->
        @if(Auth::user()->isAdmin())
            <!-- Admin 6-Column Hybrid Metrics Grid (RPK Red #C62828 Theme) -->
            <div class="bg-[#C62828] rounded-xl p-4 sm:p-5 md:p-6 shadow-xl">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4 md:gap-6">
                    <div class="space-y-0.5 sm:space-y-1">
                        <span class="text-[10px] font-bold text-white/70 uppercase tracking-[0.18em] block">Stok Fisik</span>
                        <span class="font-sans text-xl sm:text-2xl md:text-3xl font-extrabold text-white block leading-none tracking-tight truncate">
                            {{ number_format($stats['total_physical_stock'] ?? $stats['total_books']) }}
                        </span>
                        <span class="text-[10px] text-white/60">Eksemplar di rak</span>
                    </div>

                    <div class="space-y-0.5 sm:space-y-1">
                        <span class="text-[10px] font-bold text-white/70 uppercase tracking-[0.18em] block">Buku Digital</span>
                        <span class="font-sans text-xl sm:text-2xl md:text-3xl font-extrabold text-accent block leading-none tracking-tight truncate">
                            {{ number_format($stats['total_digital_books'] ?? 0) }}
                        </span>
                        <span class="text-[10px] text-white/60">Tersedia e-book</span>
                    </div>

                    <div class="space-y-0.5 sm:space-y-1">
                        <span class="text-[10px] font-bold text-white/70 uppercase tracking-[0.18em] block">Anggota</span>
                        <span class="font-sans text-xl sm:text-2xl md:text-3xl font-extrabold text-white block leading-none tracking-tight truncate">
                            {{ number_format($stats['total_members']) }}
                        </span>
                        <span class="text-[10px] text-white/60">Terdaftar aktif</span>
                    </div>

                    <div class="space-y-0.5 sm:space-y-1">
                        <span class="text-[10px] font-bold text-white/70 uppercase tracking-[0.18em] block">Pinjaman Fisik</span>
                        <span class="font-sans text-xl sm:text-2xl md:text-3xl font-extrabold text-white block leading-none tracking-tight truncate">
                            {{ number_format($stats['active_physical_loans'] ?? $stats['active_loans']) }}
                        </span>
                        <span class="text-[10px] text-white/60">Sirkulasi berjalan</span>
                    </div>

                    <div class="space-y-0.5 sm:space-y-1">
                        <span class="text-[10px] font-bold text-white/70 uppercase tracking-[0.18em] block">Akses Digital</span>
                        <span class="font-sans text-xl sm:text-2xl md:text-3xl font-extrabold text-white block leading-none tracking-tight truncate">
                            {{ number_format($stats['active_digital_loans'] ?? 0) }}
                        </span>
                        <span class="text-[10px] text-white/60">Sedang dipinjam</span>
                    </div>

                    <div class="space-y-0.5 sm:space-y-1">
                        <span class="text-[10px] font-bold text-white/70 uppercase tracking-[0.18em] block">Kurasi Esai</span>
                        <span class="font-sans text-xl sm:text-2xl md:text-3xl font-extrabold text-accent block leading-none tracking-tight truncate">
                            {{ number_format($stats['pending_essays'] ?? 0) }}
                        </span>
                        <span class="text-[10px] text-white/60">Menunggu review</span>
                    </div>
                </div>
            </div>
        @else
            <!-- Anggota 4-Column Reading Stats (RPK Red #C62828 Theme) -->
            <div class="bg-[#C62828] rounded-xl p-4 sm:p-5 md:p-6 shadow-xl">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6">
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-white/15 text-white flex items-center justify-center shrink-0 border border-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <div class="space-y-0.5 min-w-0">
                            <span class="text-[10px] font-bold text-white/70 uppercase tracking-[0.18em] block">Pinjaman Fisik</span>
                            <span class="font-sans text-xl sm:text-2xl md:text-3xl font-extrabold text-white block leading-none tracking-tight truncate">{{ $stats['my_borrowed'] ?? 0 }}</span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-white/15 text-white flex items-center justify-center shrink-0 border border-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div class="space-y-0.5 min-w-0">
                            <span class="text-[10px] font-bold text-white/70 uppercase tracking-[0.18em] block">Akses Digital</span>
                            <span class="font-sans text-xl sm:text-2xl md:text-3xl font-extrabold text-accent block leading-none tracking-tight truncate">{{ $stats['my_digital_active'] ?? 0 }}</span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-white/15 text-white flex items-center justify-center shrink-0 border border-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="space-y-0.5 min-w-0">
                            <span class="text-[10px] font-bold text-white/70 uppercase tracking-[0.18em] block">Total Riwayat</span>
                            <span class="font-sans text-xl sm:text-2xl md:text-3xl font-extrabold text-white block leading-none tracking-tight truncate">{{ $stats['my_loans'] ?? 0 }}</span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-white/15 text-white flex items-center justify-center shrink-0 border border-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </div>
                        <div class="space-y-0.5 min-w-0">
                            <span class="text-[10px] font-bold text-white/70 uppercase tracking-[0.18em] block">Esai Saya</span>
                            <span class="font-sans text-xl sm:text-2xl md:text-3xl font-extrabold text-white block leading-none tracking-tight truncate">{{ $stats['my_essays'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Content Area: 2-Column Split -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">
            
            <!-- Left 8-cols: Activities / Recent Additions -->
            <div class="lg:col-span-8 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-2 sm:gap-4 px-1">
                    <div class="min-w-0">
                        <h3 class="font-sans text-base sm:text-lg md:text-xl font-bold text-neutral-dark truncate">
                            {{ Auth::user()->isAnggota() ? 'Koleksi Terbaru untuk Ditelaah' : 'Log Sirkulasi Terkini' }}
                        </h3>
                        <p class="text-[11px] sm:text-xs text-neutral-muted mt-0.5 leading-relaxed">
                            {{ Auth::user()->isAnggota() ? 'Naskah rujukan dan bacaan yang baru saja diindeks ke dalam perpustakaan.' : 'Catatan transaksi peminjaman dan pengembalian buku fisik & digital terbaru.' }}
                        </p>
                    </div>

                    <a href="{{ Auth::user()->isAnggota() ? route('anggota.books.index') : route('admin.loans.index') }}" 
                       class="inline-flex items-center justify-center sm:justify-end gap-1 whitespace-nowrap shrink-0 text-[11px] sm:text-xs font-semibold text-primary hover:underline uppercase tracking-wider min-h-[36px] py-1">
                        <span>Lihat Seluruhnya</span>
                        <span class="shrink-0 font-sans">&rarr;</span>
                    </a>
                </div>

                @if(Auth::user()->isAnggota())
                    <!-- Member View: Curated Books List -->
                    <div class="bg-white rounded-lg border border-neutral-border divide-y divide-neutral-border shadow-xs">
                        @forelse($activities as $book)
                            <div class="p-3.5 sm:p-4 md:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 hover:bg-[#F8F8F7] transition-colors">
                                <div class="flex items-center space-x-3 sm:space-x-4 min-w-0 w-full">
                                    <div class="w-11 h-14 sm:w-12 sm:h-16 bg-[#F8F8F7] rounded overflow-hidden shrink-0 border border-neutral-border">
                                        @if($book->cover_url)
                                            <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-primary">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <span class="text-[10px] font-semibold text-primary uppercase tracking-wider">{{ $book->category->name ?? 'Umum' }}</span>
                                            @if($book->hasDigital())
                                                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">PDF</span>
                                            @endif
                                        </div>
                                        <h4 class="font-sans text-sm font-semibold text-neutral-dark truncate hover:text-primary transition-colors mt-0.5">
                                            <a href="{{ route('anggota.books.show', $book) }}">{{ $book->title }}</a>
                                        </h4>
                                        <p class="text-[11px] sm:text-xs text-neutral-muted mt-0.5 truncate">{{ $book->author }}</p>
                                    </div>
                                </div>

                                <div class="shrink-0 w-full sm:w-auto flex items-center justify-between sm:justify-end sm:space-x-3 gap-2 sm:gap-3">
                                    <span class="inline-block sm:hidden px-2 py-0.5 text-[10px] font-bold rounded {{ $book->available_stock > 0 ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-neutral-100 text-neutral-600 border border-neutral-border' }}">
                                        {{ $book->available_stock > 0 ? 'Stok: '.$book->available_stock : 'Digital' }}
                                    </span>
                                    <span class="hidden sm:inline-block px-2.5 py-0.5 text-[10px] font-bold rounded {{ $book->available_stock > 0 ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-neutral-100 text-neutral-600 border border-neutral-border' }}">
                                        {{ $book->available_stock > 0 ? 'Fisik: '.$book->available_stock : 'Hanya Digital' }}
                                    </span>
                                    <a href="{{ route('anggota.books.show', $book) }}" class="btn-editorial text-[11px] sm:text-xs py-1.5 px-3 sm:px-3.5 min-h-[36px] items-center justify-center">
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
            <div class="lg:col-span-4 space-y-5 sm:space-y-6">
                <!-- User Profile Ledger Card -->
                <div class="bg-white rounded-lg border border-neutral-border p-4 sm:p-5 md:p-6 shadow-xs space-y-5 sm:space-y-6">
                    <div class="text-center pb-4 sm:pb-5 border-b border-neutral-border">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-md bg-primary-light text-primary border border-red-200 font-sans text-xl sm:text-2xl font-bold mx-auto flex items-center justify-center mb-2 sm:mb-3 shadow-xs">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <h4 class="font-sans text-base sm:text-lg font-bold text-neutral-dark break-words">{{ Auth::user()->name }}</h4>
                        <span class="inline-block mt-1 px-2.5 py-0.5 bg-primary-light text-primary text-[10px] font-bold uppercase tracking-wider rounded border border-red-200">
                            {{ Auth::user()->isAdmin() ? 'Administrator' : 'Anggota Perpustakaan' }}
                        </span>
                    </div>

                    <div class="space-y-2.5 sm:space-y-3 text-[11px] sm:text-xs text-neutral-body">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-0.5 sm:gap-2 py-1 border-b border-neutral-border">
                            <span class="text-neutral-muted whitespace-nowrap shrink-0">Alamat Email</span>
                            <span class="font-medium text-neutral-dark break-all sm:break-words sm:text-right sm:max-w-[60%]">{{ Auth::user()->email }}</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-0.5 sm:gap-2 py-1 border-b border-neutral-border">
                            <span class="text-neutral-muted whitespace-nowrap shrink-0">Terdaftar Sejak</span>
                            <span class="font-medium text-neutral-dark">{{ Auth::user()->created_at->format('M Y') }}</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-0.5 sm:gap-2 py-1">
                            <span class="text-neutral-muted whitespace-nowrap shrink-0">Status Akun</span>
                            <x-badge variant="emerald">{{ Auth::user()->isAdmin() ? 'Otoritas Penuh' : 'Aktif / Terverifikasi' }}</x-badge>
                        </div>
                    </div>

                    <div class="pt-1 sm:pt-2">
                        <a href="{{ route('profile.edit') }}" class="btn-editorial-outline w-full py-2 sm:py-2.5 text-[11px] sm:text-xs uppercase tracking-wider font-semibold justify-center min-h-[40px]">
                            Perbarui Profil Akun
                        </a>
                    </div>
                </div>

                <!-- Academic Guidance Box with subtle gold star -->
                <div class="bg-[#F8F8F7] rounded-lg border border-neutral-border p-4 sm:p-5 md:p-6 space-y-2.5 sm:space-y-3">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-accent fill-current" viewBox="0 0 24 24">
                            <path d="M12 2l2.4 7.2h7.6l-6.2 4.5 2.4 7.3-6.2-4.6-6.2 4.6 2.4-7.3-6.2-4.5h7.6z"/>
                        </svg>
                        <span class="text-[10px] font-bold text-primary uppercase tracking-widest block">Pedoman Pembaca</span>
                    </div>
                    <h5 class="font-sans text-sm sm:text-base font-bold text-neutral-dark">Ketepatan Sirkulasi</h5>
                    <p class="text-[11px] sm:text-xs text-neutral-body leading-relaxed">
                        Pastikan setiap peminjaman fisik dikembalikan sebelum tanggal jatuh tempo. Untuk peminjaman digital, masa akses otomatis ditutup setelah {{ $digitalLoanDays }} hari.
                    </p>
                    <div class="pt-1 sm:pt-2 text-[11px] text-neutral-muted italic leading-snug">
                        Bantuan: hubungi administrator pustaka melalui layanan meja sirkulasi.
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
