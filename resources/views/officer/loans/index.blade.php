<x-app-layout>
    <x-slot name="header">
        Daftar Sirkulasi Peminjaman & Reservasi, RPK PUSTAKA
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('admin.loans.create') }}" class="btn-editorial text-xs py-2 px-4 shadow-xs">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Catat Peminjaman Baru
        </a>
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300">
        <!-- Summary Cards for Quick Filters -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <a href="{{ route('admin.loans.index', ['status' => 'pending']) }}" 
               class="p-4 rounded-lg border shadow-xs transition-all {{ request('status') === 'pending' ? 'bg-amber-50 border-amber-300 ring-2 ring-amber-400' : 'bg-white border-neutral-border hover:border-amber-300' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider">Menunggu Persetujuan</span>
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                </div>
                <span class="font-sans text-2xl font-extrabold text-amber-900 block mt-1">{{ $summary['pending'] ?? 0 }}</span>
                <span class="text-[10px] text-amber-700 block mt-0.5">Reservasi baru perlu verifikasi</span>
            </a>

            <a href="{{ route('admin.loans.index', ['status' => 'approved']) }}" 
               class="p-4 rounded-lg border shadow-xs transition-all {{ request('status') === 'approved' ? 'bg-blue-50 border-blue-300 ring-2 ring-blue-400' : 'bg-white border-neutral-border hover:border-blue-300' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-blue-800 uppercase tracking-wider">Siap Pickup / Disetujui</span>
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                </div>
                <span class="font-sans text-2xl font-extrabold text-blue-900 block mt-1">{{ $summary['approved'] ?? 0 }}</span>
                <span class="text-[10px] text-blue-700 block mt-0.5">Menunggu diambil anggota</span>
            </a>

            <a href="{{ route('admin.loans.index', ['status' => 'borrowed']) }}" 
               class="p-4 rounded-lg border shadow-xs transition-all {{ request('status') === 'borrowed' ? 'bg-emerald-50 border-emerald-300 ring-2 ring-emerald-400' : 'bg-white border-neutral-border hover:border-emerald-300' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Sedang Dipinjam</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                </div>
                <span class="font-sans text-2xl font-extrabold text-emerald-900 block mt-1">{{ $summary['borrowed'] ?? 0 }}</span>
                <span class="text-[10px] text-emerald-700 block mt-0.5">Fisik di tangan anggota</span>
            </a>

            <a href="{{ route('admin.loans.index', ['status' => 'overdue']) }}" 
               class="p-4 rounded-lg border shadow-xs transition-all {{ request('status') === 'overdue' ? 'bg-red-50 border-red-300 ring-2 ring-red-400' : 'bg-white border-neutral-border hover:border-red-300' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-red-800 uppercase tracking-wider">Terlambat Pinjam</span>
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                </div>
                <span class="font-sans text-2xl font-extrabold text-red-900 block mt-1">{{ $summary['overdue'] ?? 0 }}</span>
                <span class="text-[10px] text-red-700 block mt-0.5">Lewat batas due date</span>
            </a>
        </div>

        <!-- Filter Toolbar -->
        <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs">
            <form action="{{ route('admin.loans.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <!-- Search Input -->
                <div class="md:col-span-4 space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-dark uppercase tracking-wider block px-0.5">Pencarian</label>
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-neutral-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode transaksi, peminjam, atau judul buku..." 
                            class="w-full pl-9 pr-3 py-2 bg-neutral-surface border border-neutral-border rounded-md text-sm text-neutral-dark placeholder:text-neutral-muted focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs transition-all">
                    </div>
                </div>

                <!-- Status Filter Dropdown -->
                <div class="md:col-span-3 space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-dark uppercase tracking-wider block px-0.5">Status Transaksi</label>
                    <select name="status" 
                        class="w-full px-3 py-2 bg-neutral-surface border border-neutral-border rounded-md text-sm text-neutral-dark focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Persetujuan (Pending)</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui (Menunggu Pengambilan)</option>
                        <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Sedang Dipinjam (Borrowed)</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Telah Kembali (Returned)</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Terlambat (Overdue)</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Kedaluwarsa (Expired)</option>
                    </select>
                </div>

                <!-- Jenis Filter Dropdown -->
                <div class="md:col-span-2 space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-dark uppercase tracking-wider block px-0.5">Jenis Koleksi</label>
                    <select name="type" 
                        class="w-full px-3 py-2 bg-neutral-surface border border-neutral-border rounded-md text-sm text-neutral-dark focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                        <option value="">Semua Jenis</option>
                        <option value="physical" {{ request('type') == 'physical' ? 'selected' : '' }}>Buku Fisik</option>
                        <option value="digital" {{ request('type') == 'digital' ? 'selected' : '' }}>Buku Digital</option>
                    </select>
                </div>

                <!-- Denda Filter Dropdown -->
                <div class="md:col-span-3 flex space-x-2">
                    <x-button type="submit" variant="primary" class="flex-1 text-xs uppercase tracking-wider font-semibold py-2">
                        Filter Data
                    </x-button>
                    @if(request('search') || request('status') || request('type') || request('fine_status'))
                        <a href="{{ route('admin.loans.index') }}" class="btn-editorial-outline text-xs uppercase tracking-wider font-semibold py-2 px-3">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- ============================================== -->
        <!-- MOBILE CARD VIEW (Tampil khusus layar HP/Tablet) -->
        <!-- ============================================== -->
        <div class="block md:hidden space-y-3">
            @forelse($loans as $loan)
                <div class="bg-white rounded-lg border border-neutral-border shadow-xs overflow-hidden">
                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <span class="font-mono text-xs font-bold text-primary block leading-tight">{{ $loan->loan_code }}</span>
                                <span class="font-sans text-sm font-bold text-neutral-dark block mt-0.5">{{ $loan->user->name }}</span>
                            </div>
                            <div class="shrink-0 flex items-center gap-1.5">
                                @if($loan->isDigital())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        Digital
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-50 text-slate-700 border border-neutral-border">
                                        Fisik
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="text-xs text-neutral-dark bg-neutral-surface p-2.5 rounded border border-neutral-border/60">
                            <span class="text-[10px] text-neutral-muted block font-semibold uppercase tracking-wider mb-0.5">Koleksi:</span>
                            <span class="font-medium line-clamp-2">
                                {{ $loan->loanDetails->first()->book->title ?? 'Koleksi Perpustakaan' }}
                                @if($loan->loanDetails->count() > 1)
                                    <span class="text-accent font-semibold text-[10px] ml-1">(+{{ $loan->loanDetails->count() - 1 }} lainnya)</span>
                                @endif
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-2 pt-1 text-xs">
                            <div>
                                <span class="text-[10px] text-neutral-muted block">Batas Waktu:</span>
                                @if(($loan->isPending() || $loan->isApproved()) && $loan->pickup_deadline)
                                    <span class="font-bold text-primary text-xs">{{ $loan->pickup_deadline->format('d M Y, H:i') }}</span>
                                @else
                                    <span class="font-semibold text-neutral-dark text-xs">{{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}</span>
                                @endif
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-neutral-muted block">Status:</span>
                                @if($loan->isPending())
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300 rounded">
                                        Pending
                                    </span>
                                @elseif($loan->isApproved())
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold bg-blue-100 text-blue-900 border border-blue-300 rounded">
                                        Siap Pickup
                                    </span>
                                @elseif($loan->status === 'borrowed')
                                    @if(\Carbon\Carbon::parse($loan->due_date)->isPast())
                                        <x-badge variant="rose">Terlambat</x-badge>
                                    @else
                                        <x-badge variant="primary">Dipinjam</x-badge>
                                    @endif
                                @elseif($loan->status === 'returned')
                                    <x-badge variant="emerald">Kembali</x-badge>
                                @elseif($loan->isCancelled())
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold bg-red-50 text-red-700 border border-red-200 rounded">
                                        Dibatalkan
                                    </span>
                                @elseif($loan->isRejected())
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold bg-red-50 text-red-700 border border-red-200 rounded">
                                        Ditolak
                                    </span>
                                @elseif($loan->isExpiredState())
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-300 rounded">
                                        Kedaluwarsa
                                    </span>
                                @else
                                    <x-badge variant="rose">{{ ucfirst($loan->status) }}</x-badge>
                                @endif
                            </div>
                        </div>

                        @if($loan->fine && $loan->fine->amount > 0)
                            <div class="pt-1 flex items-center justify-between text-xs border-t border-neutral-border/60">
                                <span class="text-neutral-muted text-[11px]">Denda:</span>
                                <span class="font-mono font-bold {{ $loan->fine->status === 'paid' ? 'text-success' : 'text-danger' }}">
                                    Rp {{ number_format($loan->fine->amount, 0, ',', '.') }} ({{ $loan->fine->status === 'paid' ? 'Lunas' : 'Belum Lunas' }})
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Bottom Action Bar -->
                    <div class="bg-neutral-surface px-3.5 py-2.5 border-t border-neutral-border flex flex-wrap items-center justify-between gap-2">
                        <a href="{{ route('admin.loans.show', $loan) }}" class="btn-editorial text-xs py-1.5 px-3.5 uppercase tracking-wider font-semibold inline-flex items-center gap-1 shadow-xs">
                            Kelola Peminjaman &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-lg border border-neutral-border text-center text-xs text-neutral-muted italic">
                    Belum ada data transaksi peminjaman yang sesuai dengan filter.
                </div>
            @endforelse
        </div>

        <!-- ============================================== -->
        <!-- DESKTOP TABLE VIEW (Tampil di Laptop & PC)      -->
        <!-- ============================================== -->
        <div class="hidden md:block">
            <x-table :headers="['KODE & PEMINJAM', 'JENIS', 'KOLEKSI BUKU', 'BATAS MAKSIMAL / TENGGAT', 'STATUS SANITY', 'DENDA', 'AKSI']">
                @forelse($loans as $loan)
                    <tr class="hover:bg-[#F8F8F7] transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-xs font-bold text-primary block leading-tight">{{ $loan->loan_code }}</span>
                            <span class="font-sans text-sm font-semibold text-neutral-dark block mt-0.5">{{ $loan->user->name }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($loan->isDigital())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                    Digital
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-50 text-slate-700 border border-neutral-border">
                                    Fisik
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-neutral-dark line-clamp-1 max-w-[280px]">
                                {{ $loan->loanDetails->first()->book->title ?? 'Koleksi Perpustakaan' }}
                                @if($loan->loanDetails->count() > 1)
                                    <span class="text-accent font-semibold text-[10px] ml-1">(+{{ $loan->loanDetails->count() - 1 }} lainnya)</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-neutral-body">
                            @if(($loan->isPending() || $loan->isApproved()) && $loan->pickup_deadline)
                                <span class="font-bold text-primary block">{{ $loan->pickup_deadline->format('d M Y, H:i') }}</span>
                                <span class="text-[10px] text-amber-700">Pickup Deadline</span>
                            @else
                                <span class="font-medium text-neutral-dark block">{{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}</span>
                                @if($loan->status === 'borrowed')
                                    <span class="text-[10px] text-neutral-muted">{{ ceil(now()->diffInDays($loan->due_date, false)) }} hari tersisa</span>
                                @endif
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($loan->isPending())
                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300 rounded">
                                    🔒 Pending Reservasi
                                </span>
                            @elseif($loan->isApproved())
                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold bg-blue-100 text-blue-900 border border-blue-300 rounded">
                                    ✓ Disetujui (Siap Pickup)
                                </span>
                            @elseif($loan->status === 'borrowed')
                                @if(\Carbon\Carbon::parse($loan->due_date)->isPast())
                                    <x-badge variant="rose">Terlambat Pinjam</x-badge>
                                @else
                                    <x-badge variant="primary">Sedang Dipinjam</x-badge>
                                @endif
                            @elseif($loan->status === 'returned')
                                <x-badge variant="emerald">Telah Kembali</x-badge>
                            @elseif($loan->isCancelled())
                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold bg-red-50 text-red-700 border border-red-200 rounded">
                                    Dibatalkan
                                </span>
                            @elseif($loan->isRejected())
                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold bg-red-50 text-red-700 border border-red-200 rounded">
                                    Ditolak
                                </span>
                            @elseif($loan->isExpiredState())
                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-300 rounded">
                                    Kedaluwarsa
                                </span>
                            @else
                                <x-badge variant="rose">{{ ucfirst($loan->status) }}</x-badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($loan->fine)
                                @if($loan->fine->status === 'unpaid')
                                    <div class="space-y-0.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-danger border border-red-200">
                                            Belum Lunas
                                        </span>
                                        <span class="block text-xs font-mono font-bold text-danger">
                                            Rp {{ number_format($loan->fine->amount, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @else
                                    <div class="space-y-0.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-[#EDF7ED] text-success border border-[#C8E6C9]">
                                            Lunas
                                        </span>
                                        <span class="block text-[11px] font-mono text-neutral-muted">
                                            Rp {{ number_format($loan->fine->amount, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endif
                            @else
                                <span class="text-xs text-neutral-muted font-mono italic">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <a href="{{ route('admin.loans.show', $loan) }}" class="btn-editorial-outline text-xs py-1.5 px-3 uppercase tracking-wider font-semibold">
                                Kelola &rarr;
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-xs text-neutral-muted italic">
                            Belum ada data transaksi peminjaman yang sesuai dengan filter.
                        </td>
                    </tr>
                @endforelse
            </x-table>
        </div>

        <div class="pt-4">
            {{ $loans->links() }}
        </div>
    </div>
</x-app-layout>
