<x-app-layout>
    <x-slot name="header">
        Buku Pinjaman & Riwayat Sirkulasi, RPK PUSTAKA IMM SAINTEKMU
    </x-slot>

    <div class="space-y-8 animate-in fade-in duration-300">
        <!-- Editorial Section Lead -->
        <div class="space-y-1">
            <span class="text-xs uppercase tracking-[0.2em] font-semibold text-primary block">Arsip Pribadi Anggota</span>
            <h2 class="font-sans text-2xl sm:text-3xl font-bold text-neutral-dark tracking-tight">Catatan Peminjaman & Status Sirkulasi</h2>
            <p class="text-xs sm:text-sm text-neutral-body">Daftar seluruh riwayat reservasi buku fisik, peminjaman aktif, status pengembalian, serta rincian denda.</p>
        </div>

        <!-- Academic Metrics Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded bg-primary-light text-primary flex items-center justify-center shrink-0 border border-red-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block">Total Transaksi</span>
                    <span class="font-sans text-xl font-bold text-neutral-dark block leading-tight">{{ $loans->total() }}</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block">Reservasi Pending</span>
                    <span class="font-sans text-xl font-bold text-amber-700 block leading-tight">
                        {{ $loans->where('status', 'pending')->count() }}
                    </span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded bg-[#EDF7ED] text-success flex items-center justify-center shrink-0 border border-[#C8E6C9]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block">Buku Fisik Dipinjam</span>
                    <span class="font-sans text-xl font-bold text-neutral-dark block leading-tight">
                        {{ $loans->where('loan_type', 'physical')->where('status', 'borrowed')->count() }}
                    </span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded bg-red-50 text-danger flex items-center justify-center shrink-0 border border-red-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block">Tagihan Denda Aktif</span>
                    <span class="font-sans text-lg font-bold text-danger block leading-tight">
                        @php
                            $unpaidFines = $loans->sum(function($l) {
                                return ($l->fine && $l->fine->status === 'unpaid') ? $l->fine->amount : 0;
                            });
                        @endphp
                        Rp {{ number_format($unpaidFines, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 border-b border-neutral-border pb-3 overflow-x-auto">
            <a href="{{ route('anggota.loans.index') }}" 
               class="px-3.5 py-1.5 rounded text-xs font-semibold transition-colors whitespace-nowrap {{ !request('type') && !request('status') ? 'bg-primary text-white' : 'bg-white text-neutral-body border border-neutral-border hover:border-primary hover:text-primary' }}">
                Semua Peminjaman
            </a>
            <a href="{{ route('anggota.loans.index', ['status' => 'pending']) }}" 
               class="px-3.5 py-1.5 rounded text-xs font-semibold transition-colors whitespace-nowrap {{ request('status') === 'pending' ? 'bg-primary text-white' : 'bg-white text-neutral-body border border-neutral-border hover:border-primary hover:text-primary' }}">
                Menunggu Persetujuan
            </a>
            <a href="{{ route('anggota.loans.index', ['status' => 'approved']) }}" 
               class="px-3.5 py-1.5 rounded text-xs font-semibold transition-colors whitespace-nowrap {{ request('status') === 'approved' ? 'bg-primary text-white' : 'bg-white text-neutral-body border border-neutral-border hover:border-primary hover:text-primary' }}">
                Menunggu Pengambilan
            </a>
            <a href="{{ route('anggota.loans.index', ['type' => 'physical']) }}" 
               class="px-3.5 py-1.5 rounded text-xs font-semibold transition-colors whitespace-nowrap flex items-center gap-1.5 {{ request('type') === 'physical' ? 'bg-primary text-white' : 'bg-white text-neutral-body border border-neutral-border hover:border-primary hover:text-primary' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                Peminjaman Fisik
            </a>
            <a href="{{ route('anggota.loans.index', ['type' => 'digital']) }}" 
               class="px-3.5 py-1.5 rounded text-xs font-semibold transition-colors whitespace-nowrap flex items-center gap-1.5 {{ request('type') === 'digital' ? 'bg-primary text-white' : 'bg-white text-neutral-body border border-neutral-border hover:border-primary hover:text-primary' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Digital (E-Book)
            </a>
        </div>

        <!-- Loans Circulation Table -->
        <x-table :headers="['KODE & TANGGAL', 'VOLUME BUKU', 'BATAS MAKSIMAL / TENGGAT', 'STATUS SANITY', 'RINCIAN DENDA', 'AKSI & BUKTI']">
            @forelse($loans as $loan)
                <tr class="group transition-colors hover:bg-[#F8F8F7]">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono text-xs font-bold text-primary block">{{ $loan->loan_code }}</span>
                        <span class="text-[10px] text-neutral-muted block mt-0.5">Pengajuan: {{ $loan->created_at->format('d M Y H:i') }}</span>
                        @if($loan->isDigital())
                            <span class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-[#FFF9ED] text-[#B45309] border border-[#FDE68A]">
                                Digital PDF
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-neutral-100 text-neutral-dark border border-neutral-200">
                                Buku Fisik
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4">
                        <div class="space-y-1.5">
                            @foreach($loan->loanDetails as $detail)
                                @if($detail->book)
                                    <div class="flex items-start text-xs">
                                        <span class="w-1.5 h-1.5 bg-primary rounded-full mr-2 mt-1.5 shrink-0"></span>
                                        <div class="min-w-0">
                                            <a href="{{ route('anggota.books.show', $detail->book) }}" class="font-semibold text-neutral-dark hover:text-primary transition-colors block line-clamp-1">
                                                {{ $detail->book->title }}
                                            </a>
                                            <span class="text-[10px] text-neutral-muted font-mono block">
                                                {{ $detail->book->author }} • Rak: {{ $detail->book->location->name ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        @if(($loan->isPending() || $loan->isApproved()) && $loan->pickup_deadline)
                            <div class="text-xs font-bold text-primary">
                                {{ $loan->pickup_deadline->format('d M Y, H:i') }} WIB
                            </div>
                            <span class="text-[10px] font-medium text-amber-700 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded inline-block mt-0.5">
                                Batas Pengambilan Buku
                            </span>
                        @else
                            <div class="text-xs font-bold text-neutral-dark">
                                {{ $loan->due_date->format('d M Y') }}
                            </div>
                            @if($loan->status === 'borrowed')
                                @php
                                    $daysRemaining = ceil(now()->startOfDay()->diffInDays($loan->due_date, false));
                                @endphp
                                <p @class([
                                    'text-[10px] font-medium mt-0.5',
                                    'text-danger font-bold' => $daysRemaining < 0,
                                    'text-danger font-semibold' => $daysRemaining >= 0 && $daysRemaining <= 2,
                                    'text-neutral-muted' => $daysRemaining > 2,
                                ])>
                                    {{ $daysRemaining > 0 ? $daysRemaining . ' hari tersisa' : ($daysRemaining === 0 ? 'Hari ini tenggat' : 'Terlambat ' . abs($daysRemaining) . ' hari') }}
                                </p>
                            @else
                                <p class="text-[10px] text-neutral-muted font-medium mt-0.5">-</p>
                            @endif
                        @endif
                    </td>

                    <!-- Status Column -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($loan->isPending())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 rounded">
                                🔒 Menunggu Persetujuan
                            </span>
                        @elseif($loan->isApproved())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200 rounded">
                                ✓ Disetujui (Siap Pick Up)
                            </span>
                        @elseif($loan->isBorrowed())
                            @if(\Carbon\Carbon::parse($loan->due_date)->isPast())
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold bg-red-50 text-danger border border-red-200 rounded">
                                    Terlambat (Aktif)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 rounded">
                                    Sedang Dipinjam
                                </span>
                            @endif
                        @elseif($loan->isReturned())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 rounded">
                                ✓ Dikembalikan
                            </span>
                        @elseif($loan->isCancelled())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold bg-red-50 text-red-700 border border-red-200 rounded">
                                ✕ Dibatalkan
                            </span>
                        @elseif($loan->isRejected())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold bg-red-50 text-red-700 border border-red-200 rounded">
                                ✕ Ditolak Admin
                            </span>
                        @elseif($loan->isExpiredState())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-300 rounded">
                                ⏱️ Kedaluwarsa
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold bg-neutral-100 text-neutral-dark rounded">
                                {{ ucfirst($loan->status) }}
                            </span>
                        @endif
                    </td>

                    <!-- Informasi Rincian Denda Column -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($loan->fine && $loan->fine->amount > 0)
                            <div>
                                <span class="font-sans text-xs font-bold {{ $loan->fine->status === 'paid' ? 'text-neutral-dark' : 'text-danger' }}">
                                    Rp {{ number_format($loan->fine->amount, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-neutral-muted block font-mono">
                                    {{ ucfirst($loan->fine->type) }}
                                </span>
                                @if($loan->fine->status === 'paid')
                                    <span class="inline-flex items-center gap-1 text-[9px] font-bold text-success bg-[#EDF7ED] border border-[#C8E6C9] px-1.5 py-0.2 rounded mt-0.5">
                                        ✓ LUNAS
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[9px] font-bold text-danger bg-red-50 border border-red-200 px-1.5 py-0.2 rounded mt-0.5">
                                        ✕ BELUM DIBAYAR
                                    </span>
                                @endif
                            </div>
                        @else
                            <span class="text-[11px] font-mono text-neutral-muted">Bebas Denda</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                        <!-- Link Bukti Peminjaman -->
                        <a href="{{ route('anggota.loans.show', $loan) }}" 
                           class="btn-editorial-outline text-[11px] py-1 px-2.5 font-semibold inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Bukti
                        </a>

                        @if($loan->isDigital() && $loan->isActive())
                            @php
                                $book = $loan->loanDetails->first()?->book;
                            @endphp
                            @if($book)
                                <a href="{{ route('anggota.books.reader', $book) }}" 
                                   class="btn-editorial text-[11px] py-1 px-2.5 uppercase font-semibold inline-flex items-center gap-1 shadow-xs">
                                    Baca
                                </a>
                            @endif
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="w-12 h-12 rounded-full bg-primary-light text-primary flex items-center justify-center mx-auto mb-3 border border-red-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <p class="font-sans text-base font-bold text-neutral-dark">Belum Ada Riwayat Peminjaman</p>
                        <p class="text-xs text-neutral-muted mt-1">Anda belum memiliki peminjaman buku fisik maupun digital di RPK PUSTAKA IMM SAINTEKMU.</p>
                        <div class="mt-4">
                            <a href="{{ route('anggota.books.index') }}" class="btn-editorial text-xs py-2 px-5">
                                Jelajahi Katalog Buku
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="pt-4">
            {{ $loans->links() }}
        </div>
    </div>
</x-app-layout>
