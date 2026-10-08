<x-app-layout>
    <x-slot name="header">
        Detail Koleksi Buku & Status Reservasi
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('admin.books.index') }}" class="btn-editorial-outline text-xs py-2 px-3.5 uppercase tracking-wider inline-flex items-center">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
        @if($book->hasDigital())
            <a href="{{ route('admin.books.reader', $book) }}" target="_blank" class="px-3.5 py-2 text-xs font-bold uppercase tracking-wider bg-white text-[#B45309] border border-amber-200 hover:bg-amber-50 rounded transition-colors inline-flex items-center shadow-md">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Baca Digital
            </a>
        @endif
        <a href="{{ route('admin.books.edit', $book) }}" class="btn-editorial text-xs py-2 px-4 uppercase tracking-wider inline-flex items-center">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            Edit Buku
        </a>
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300">
        <!-- Book Identity & Breadcrumb Banner Card -->
        <div class="bg-white p-5 rounded-lg border border-neutral-border shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1.5">
                <nav class="flex items-center gap-2 text-[11px] text-neutral-muted uppercase tracking-wider font-mono">
                    <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dasbor</a>
                    <span>/</span>
                    <a href="{{ route('admin.books.index') }}" class="hover:text-primary transition-colors">Katalog Buku</a>
                    <span>/</span>
                    <span class="text-primary font-bold">{{ $book->book_code ?? 'DETAIL' }}</span>
                </nav>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h2 class="text-xl sm:text-2xl font-bold text-neutral-dark tracking-tight leading-snug">
                        {{ $book->title }}
                    </h2>
                    @if($book->collection_type === 'fisik_digital')
                        <span class="inline-flex items-center px-2.5 py-0.5 text-[10px] font-bold bg-primary-light text-primary border border-red-200 rounded">
                            Fisik & Digital
                        </span>
                    @elseif($book->collection_type === 'digital')
                        <span class="inline-flex items-center px-2.5 py-0.5 text-[10px] font-bold bg-[#FFF9ED] text-[#B45309] border border-[#FDE68A] rounded">
                            Digital
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 text-[10px] font-bold bg-[#EDF7ED] text-success border border-[#C8E6C9] rounded">
                            Fisik
                        </span>
                    @endif
                </div>
                <p class="text-xs text-neutral-body">
                    Penulis: <span class="font-semibold text-neutral-dark">{{ $book->author }}</span> • 
                    Kategori: <span class="font-semibold text-primary">{{ $book->category->name ?? 'Umum' }}</span> • 
                    Kode Buku: <span class="font-mono font-bold text-neutral-dark">{{ $book->book_code ?? '-' }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0 pt-1 sm:pt-0">
                <a href="{{ route('admin.books.index') }}" class="btn-editorial-outline text-xs py-2 px-3.5 uppercase tracking-wider inline-flex items-center justify-center w-full sm:w-auto">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Katalog
                </a>
            </div>
        </div>

        <!-- Main Info Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Left Column: Cover & Quick Specs (4 cols) -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Cover Card -->
                <div class="bg-white p-4 sm:p-5 rounded-lg border border-neutral-border shadow-xs">
                    <div class="aspect-[3/4.2] bg-neutral-surface rounded overflow-hidden relative border border-neutral-border shadow-xs flex items-center justify-center">
                        @if($book->cover_url)
                            <img id="detail-cover-img" src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center p-6 text-center bg-neutral-surface">
                                <div class="w-14 h-14 rounded-full bg-white flex items-center justify-center text-primary mb-3 shadow-xs border border-neutral-border">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </div>
                                <span class="font-sans text-sm font-bold text-neutral-dark">{{ $book->title }}</span>
                                <span class="text-xs text-neutral-muted mt-1">{{ $book->author }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Availability & Stock breakdown Card -->
                <div class="bg-white p-5 rounded-lg border border-neutral-border shadow-xs space-y-4">
                    <h3 class="text-xs font-bold text-neutral-dark uppercase tracking-wider pb-2 border-b border-neutral-border">
                        Status Stok & Reservasi
                    </h3>

                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between py-1 border-b border-neutral-border/60">
                            <span class="text-neutral-muted">Total Stok Fisik:</span>
                            <span class="font-bold text-neutral-dark font-mono">{{ $book->stock }} Eksemplar</span>
                        </div>

                        <div class="flex items-center justify-between py-1 border-b border-neutral-border/60">
                            <span class="text-neutral-muted">Stok Tersedia (Available):</span>
                            <div>
                                <span class="font-extrabold font-mono text-base {{ $book->available_stock > 0 ? 'text-success' : 'text-danger' }}">{{ $book->available_stock }}</span>
                                <span class="text-neutral-muted font-mono">/ {{ $book->stock }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between py-1 border-b border-neutral-border/60">
                            <span class="text-neutral-muted">Status Eksemplar:</span>
                            @if($book->available_stock > 0)
                                <span class="px-2 py-0.5 text-[10px] font-bold bg-[#EDF7ED] text-success border border-[#C8E6C9] rounded">
                                    Tersedia Dipesan
                                </span>
                            @elseif($book->stock > 0)
                                <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-50 text-amber-900 border border-amber-300 rounded">
                                    🔒 RESERVED / DIPINJAM
                                </span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-bold bg-neutral-100 text-neutral-body rounded">
                                    Digital Saja
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between py-1 border-b border-neutral-border/60">
                            <span class="text-neutral-muted">Lokasi Rak Simpan:</span>
                            <span class="font-semibold text-neutral-dark uppercase font-mono">
                                {{ $book->location->name ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Active Reservations Card -->
                <div class="bg-white p-5 rounded-lg border border-neutral-border shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-neutral-border">
                        <h3 class="text-xs font-bold text-neutral-dark uppercase tracking-wider">
                            Reservasi Aktif ({{ count($activeReservations ?? []) }})
                        </h3>
                    </div>

                    <div class="space-y-3">
                        @forelse($activeReservations ?? [] as $detail)
                            @php $loan = $detail->loan; @endphp
                            @if($loan)
                                <div class="p-3 bg-neutral-surface border border-neutral-border rounded text-xs space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="font-mono font-bold text-primary">{{ $loan->loan_code }}</span>
                                        @if($loan->isPending())
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-100 text-amber-900 border border-amber-300">Menunggu Persetujuan</span>
                                        @elseif($loan->isApproved())
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-blue-100 text-blue-900 border border-blue-300">Disetujui (Pickup)</span>
                                        @elseif($loan->isBorrowed())
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-900 border border-emerald-300">Dipinjam</span>
                                        @endif
                                    </div>

                                    <p class="font-bold text-neutral-dark">{{ $loan->user->name ?? 'Anggota' }}</p>

                                    @if($loan->pickup_deadline && ($loan->isPending() || $loan->isApproved()))
                                        <p class="text-[10px] text-red-600 font-semibold">
                                            Batas Pickup: {{ $loan->pickup_deadline->format('d M Y H:i') }}
                                        </p>
                                    @endif

                                    <div class="pt-1 text-right">
                                        <a href="{{ route('admin.loans.show', $loan) }}" class="text-[10px] font-bold text-primary hover:underline">
                                            Kelola Transaksi &rarr;
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <p class="text-xs text-neutral-muted italic py-2">Tidak ada reservasi aktif saat ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column: Bibliographic Details & Circulation History (8 cols) -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Bibliographic Details Card -->
                <div class="bg-white p-6 rounded-lg border border-neutral-border shadow-xs space-y-6">
                    <div class="pb-3 border-b border-neutral-border flex items-center justify-between">
                        <h3 class="text-sm font-bold text-neutral-dark uppercase tracking-wider">
                            Informasi Bibliografi Naskah
                        </h3>
                        <span class="text-xs font-mono font-bold text-primary px-2.5 py-1 bg-primary-light border border-red-200 rounded">
                            {{ $book->book_code ?? '-' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                        <div>
                            <span class="text-neutral-muted uppercase tracking-wider block font-semibold text-[10px] mb-1">Judul Buku</span>
                            <span class="text-sm font-bold text-neutral-dark block leading-snug">{{ $book->title }}</span>
                        </div>

                        <div>
                            <span class="text-neutral-muted uppercase tracking-wider block font-semibold text-[10px] mb-1">Penulis / Pengarang</span>
                            <span class="text-sm font-semibold text-neutral-dark block">{{ $book->author }}</span>
                        </div>

                        <div>
                            <span class="text-neutral-muted uppercase tracking-wider block font-semibold text-[10px] mb-1">Kategori / Klasifikasi</span>
                            <span class="inline-block mt-0.5 font-semibold text-primary bg-primary-light border border-red-200 px-2 py-0.5 rounded">
                                {{ $book->category->name ?? 'Umum' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-neutral-muted uppercase tracking-wider block font-semibold text-[10px] mb-1">Nomor ISBN</span>
                            <span class="text-xs font-mono font-semibold text-neutral-dark block">{{ $book->isbn ?? '-' }}</span>
                        </div>

                        <div>
                            <span class="text-neutral-muted uppercase tracking-wider block font-semibold text-[10px] mb-1">Penerbit</span>
                            <span class="text-xs font-semibold text-neutral-dark block">{{ $book->publisher ?? '-' }}</span>
                        </div>

                        <div>
                            <span class="text-neutral-muted uppercase tracking-wider block font-semibold text-[10px] mb-1">Tahun Terbit</span>
                            <span class="text-xs font-semibold text-neutral-dark block">{{ $book->year ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Synopsis / Description -->
                    <div class="pt-4 border-t border-neutral-border">
                        <span class="text-neutral-muted uppercase tracking-wider block font-semibold text-[10px] mb-2">Sinopsis / Ringkasan Koleksi</span>
                        @if($book->description)
                            <div class="text-xs text-neutral-body leading-relaxed whitespace-pre-line bg-neutral-surface p-4 rounded border border-neutral-border">
                                {{ $book->description }}
                            </div>
                        @else
                            <p class="text-xs text-neutral-muted italic bg-neutral-surface p-4 rounded border border-neutral-border">
                                Belum ada deskripsi atau ringkasan sinopsis untuk buku ini.
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Recent Loan Activity Card -->
                <div class="bg-white rounded-lg border border-neutral-border overflow-hidden shadow-xs">
                    <div class="px-6 py-4 border-b border-neutral-border bg-neutral-surface flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                            <h3 class="text-sm font-bold text-neutral-dark uppercase tracking-wider">
                                Riwayat Sirkulasi Pinjaman Terkini
                            </h3>
                        </div>
                        <span class="text-xs text-neutral-muted font-mono">Total Transaksi: {{ count($recentLoans) }}</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-neutral-dark divide-y divide-neutral-border">
                            <thead class="bg-neutral-surface text-[10px] uppercase font-bold text-neutral-muted tracking-wider">
                                <tr>
                                    <th class="px-6 py-3">Peminjam</th>
                                    <th class="px-6 py-3">Tipe</th>
                                    <th class="px-6 py-3">Tgl Pinjam</th>
                                    <th class="px-6 py-3">Tenggat</th>
                                    <th class="px-6 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-border">
                                @forelse($recentLoans as $detail)
                                    @php $loan = $detail->loan; @endphp
                                    @if($loan)
                                        <tr class="hover:bg-neutral-surface/60 transition-colors">
                                            <td class="px-6 py-3.5 whitespace-nowrap">
                                                <div class="font-semibold text-neutral-dark">{{ $loan->user->name ?? 'Anggota' }}</div>
                                                <div class="text-[10px] text-neutral-muted font-mono">{{ $loan->loan_code }}</div>
                                            </td>
                                            <td class="px-6 py-3.5 whitespace-nowrap">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $loan->loan_type === 'digital' ? 'bg-[#FFF9ED] text-[#B45309]' : 'bg-[#EDF7ED] text-success' }}">
                                                    {{ $loan->loan_type ?? 'Fisik' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-3.5 whitespace-nowrap text-neutral-body">
                                                {{ $loan->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-3.5 whitespace-nowrap text-neutral-body font-mono">
                                                {{ $loan->due_date ? $loan->due_date->format('d/m/Y') : '-' }}
                                            </td>
                                            <td class="px-6 py-3.5 whitespace-nowrap">
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded border
                                                    @if($loan->isPending()) bg-amber-100 text-amber-900 border-amber-300
                                                    @elseif($loan->isApproved()) bg-blue-100 text-blue-900 border-blue-300
                                                    @elseif($loan->isBorrowed()) bg-emerald-100 text-emerald-900 border-emerald-300
                                                    @elseif($loan->isReturned()) bg-slate-100 text-slate-800 border-slate-300
                                                    @else bg-red-100 text-red-900 border-red-300 @endif">
                                                    {{ $loan->status_label }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-xs text-neutral-muted italic">
                                            Belum ada catatan sirkulasi peminjaman untuk buku ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
