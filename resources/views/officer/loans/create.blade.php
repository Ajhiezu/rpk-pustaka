<x-app-layout>
    <x-slot name="header">
        Pencatatan Peminjaman Koleksi
    </x-slot>

    @php
        $physicalLoanDays = max(1, (int) \App\Models\Setting::get('physical_loan_duration_days', 14));
    @endphp

    <div class="max-w-4xl mx-auto space-y-6 animate-in fade-in duration-300" 
         x-data="{
            bookSearch: '',
            selectedBookIds: @json(array_map('strval', old('book_ids', []))),
            filterSelectedOnly: false,

            toggleBook(id) {
                const strId = String(id);
                if (this.selectedBookIds.includes(strId)) {
                    this.selectedBookIds = this.selectedBookIds.filter(i => i !== strId);
                } else {
                    this.selectedBookIds.push(strId);
                }
            },
            isBookSelected(id) {
                return this.selectedBookIds.includes(String(id));
            }
         }">

        <div>
            <a href="{{ route('admin.loans.index') }}" 
               class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-neutral-muted hover:text-primary transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Daftar Transaksi
            </a>
        </div>

        <x-card>
            <x-slot name="header">Formulir Sirkulasi Peminjaman Baru</x-slot>

            <form action="{{ route('admin.loans.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- ============================================== -->
                <!-- SEKSI 1: SEARCHABLE DROPDOWN ANGGOTA          -->
                <!-- ============================================== -->
                <div x-data="{
                    open: false,
                    search: '',
                    selectedId: '{{ old('user_id') }}',
                    selectedName: '',
                    selectedEmail: '',
                    selectedCode: '',

                    borrowers: [
                        @foreach($borrowers as $b)
                        {
                            id: '{{ $b->id }}',
                            name: '{{ addslashes($b->name) }}',
                            email: '{{ addslashes($b->email) }}',
                            code: 'ANG-{{ str_pad($b->id, 4, '0', STR_PAD_LEFT) }}',
                            role: '{{ strtoupper($b->role) }}'
                        },
                        @endforeach
                    ],

                    init() {
                        if (this.selectedId) {
                            const found = this.borrowers.find(b => b.id == this.selectedId);
                            if (found) {
                                this.selectMember(found);
                            }
                        }
                    },

                    get filteredBorrowers() {
                        if (!this.search.trim()) return this.borrowers;
                        const q = this.search.toLowerCase().trim();
                        return this.borrowers.filter(b => 
                            b.name.toLowerCase().includes(q) || 
                            b.email.toLowerCase().includes(q) || 
                            b.code.toLowerCase().includes(q)
                        );
                    },

                    selectMember(b) {
                        this.selectedId = b.id;
                        this.selectedName = b.name;
                        this.selectedEmail = b.email;
                        this.selectedCode = b.code;
                        this.open = false;
                        this.search = '';
                    },

                    clearMember() {
                        this.selectedId = '';
                        this.selectedName = '';
                        this.selectedEmail = '';
                        this.selectedCode = '';
                        this.search = '';
                    }
                }" 
                x-init="init()"
                class="relative space-y-2 pb-5 border-b border-neutral-border"
                @click.outside="open = false">

                    <input type="hidden" name="user_id" :value="selectedId" required>

                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-neutral-dark uppercase tracking-wider">
                            1. Pilih Anggota Peminjam *
                        </label>
                        <span class="text-[11px] font-semibold" :class="selectedId ? 'text-emerald-700' : 'text-neutral-muted'" x-text="selectedId ? '✓ Anggota Terpilih' : 'Klik untuk mencari & memilih anggota'"></span>
                    </div>

                    <!-- Trigger Button when no member selected -->
                    <div x-show="!selectedId">
                        <button type="button" @click="open = !open; if (open) $nextTick(() => $refs.memberSearchInput.focus());" 
                                class="w-full flex items-center justify-between px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-xs text-neutral-muted hover:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all shadow-xs">
                            <span class="font-normal" x-text="open ? 'Pencarian aktif...' : 'Pilih atau cari Anggota Peminjam...'"></span>
                            <svg class="w-4 h-4 text-neutral-muted transition-transform shrink-0" :class="open ? 'rotate-180 text-primary' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>

                    <!-- Selected Member Display Card -->
                    <div x-show="selectedId" x-cloak class="p-3 bg-[#F8F8F7] border border-neutral-border rounded-md flex items-center justify-between gap-3 shadow-xs">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-primary-light text-primary flex items-center justify-center font-bold text-xs shrink-0 border border-red-200">
                                <span x-text="selectedName ? selectedName.charAt(0).toUpperCase() : 'A'"></span>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center space-x-2">
                                    <span class="font-sans text-xs font-bold text-neutral-dark truncate" x-text="selectedName"></span>
                                    <span class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-white border border-neutral-border text-neutral-muted shrink-0" x-text="selectedCode"></span>
                                </div>
                                <span class="text-[11px] text-neutral-body block truncate" x-text="selectedEmail"></span>
                            </div>
                        </div>
                        <button type="button" @click="clearMember(); open = true; $nextTick(() => $refs.memberSearchInput.focus());" class="text-xs text-danger hover:text-red-700 font-semibold px-2.5 py-1 rounded bg-red-50 hover:bg-red-100 transition-colors shrink-0">
                            Ganti Anggota
                        </button>
                    </div>

                    <!-- Floating Dropdown Panel with Integrated Search Field -->
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute z-30 left-0 right-0 top-full mt-1 bg-white border border-neutral-border rounded-lg shadow-lg overflow-hidden">
                        
                        <!-- Embedded Search Input -->
                        <div class="p-2.5 bg-[#F8F8F7] border-b border-neutral-border">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-neutral-muted">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" x-model="search" x-ref="memberSearchInput"
                                       @keydown.escape="open = false"
                                       placeholder="Cari nama, email, atau ID anggota..."
                                       class="w-full pl-9 pr-8 py-2 bg-white border border-neutral-border rounded text-xs text-neutral-dark placeholder:text-neutral-muted focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                                <button type="button" x-show="search" @click="search = ''" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-neutral-muted hover:text-neutral-dark">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Filtered Member List -->
                        <div class="max-h-56 overflow-y-auto divide-y divide-neutral-border/50 custom-scrollbar">
                            <template x-for="b in filteredBorrowers" :key="b.id">
                                <button type="button" @click="selectMember(b)" 
                                        class="w-full text-left p-3 hover:bg-[#F8F8F7] flex items-center justify-between transition-colors group">
                                    <div class="min-w-0 pr-2">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-sans text-xs font-bold text-neutral-dark group-hover:text-primary transition-colors truncate" x-text="b.name"></span>
                                            <span class="text-[9px] font-mono px-1.5 py-0.2 rounded bg-neutral-100 text-neutral-muted shrink-0" x-text="b.code"></span>
                                        </div>
                                        <span class="text-[11px] text-neutral-body block truncate mt-0.5" x-text="b.email"></span>
                                    </div>
                                    <svg x-show="selectedId == b.id" class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </button>
                            </template>
                            <div x-show="filteredBorrowers.length === 0" class="p-4 text-center text-xs text-neutral-muted">
                                Tidak ada data anggota yang cocok dengan kata kunci.
                            </div>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('user_id')" class="mt-1.5" />
                </div>

                <!-- ============================================== -->
                <!-- SEKSI 2: BATAS WAKTU PENGEMBALIAN             -->
                <!-- ============================================== -->
                <div class="p-4 bg-[#F8F8F7] rounded-md border border-neutral-border space-y-2">
                    <label for="due_date" class="block text-xs font-bold text-neutral-dark uppercase tracking-wider">
                        2. Batas Waktu Pengembalian (Jatuh Tempo) *
                    </label>
                    <input type="date" name="due_date" id="due_date" required 
                        min="{{ now()->addDay()->toDateString() }}" 
                        max="{{ now()->addDays($physicalLoanDays)->toDateString() }}"
                        value="{{ old('due_date', now()->addDays($physicalLoanDays)->toDateString()) }}"
                        class="w-full px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-xs font-semibold text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                    <p class="text-[11px] text-neutral-muted italic">* Standar durasi peminjaman fisik mengikuti pengaturan admin: maksimal {{ $physicalLoanDays }} hari kalender.</p>
                    <x-input-error :messages="$errors->get('due_date')" class="mt-1.5" />
                </div>

                <!-- ============================================== -->
                <!-- SEKSI 3: PENCARIAN & PEMILIHAN KOLEKSI BUKU   -->
                <!-- ============================================== -->
                <div class="space-y-3 pt-2">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <label class="block text-xs font-bold text-neutral-dark uppercase tracking-wider">
                            3. Pilih Koleksi Buku yang Dipinjam *
                        </label>
                        
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-bold text-primary" x-text="selectedBookIds.length + ' buku dipilih'"></span>
                            <button type="button" @click="filterSelectedOnly = !filterSelectedOnly" 
                                    class="px-2.5 py-1 text-[10px] font-bold uppercase rounded border transition-colors"
                                    :class="filterSelectedOnly ? 'bg-primary text-white border-primary' : 'bg-neutral-surface text-neutral-dark border-neutral-border hover:bg-neutral-border/50'">
                                <span x-text="filterSelectedOnly ? 'Lihat Semua Buku' : 'Hanya Terpilih'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Search Input for Books with Professional SVG Icon -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-neutral-muted">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="bookSearch" placeholder="Cari judul buku, ISBN, nama penulis, atau lokasi rak..." 
                               class="w-full pl-9 pr-8 py-2.5 bg-white border border-neutral-border rounded-md text-xs text-neutral-dark placeholder:text-neutral-muted focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                        <button type="button" x-show="bookSearch" @click="bookSearch = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center text-neutral-muted hover:text-neutral-dark">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Scrollable Book List Container -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-96 overflow-y-auto p-4 bg-[#F8F8F7] rounded-md border border-neutral-border custom-scrollbar">
                        @foreach($books as $book)
                            @php
                                $bookSearchHaystack = strtolower($book->title . ' ' . $book->author . ' ' . ($book->isbn ?? '') . ' ' . ($book->location->name ?? 'Rak Utama') . ' rpk-b' . str_pad($book->id, 4, '0', STR_PAD_LEFT));
                            @endphp
                            <label x-show="(!bookSearch || '{{ addslashes($bookSearchHaystack) }}'.includes(bookSearch.toLowerCase().trim())) && (!filterSelectedOnly || isBookSelected('{{ $book->id }}'))"
                                   class="relative flex items-start p-3.5 bg-white rounded-lg border cursor-pointer transition-all group shadow-xs hover:border-primary"
                                   :class="isBookSelected('{{ $book->id }}') ? 'border-primary bg-primary-light/40 ring-1 ring-primary/30' : 'border-neutral-border hover:bg-neutral-surface'">
                                
                                <input type="checkbox" name="book_ids[]" value="{{ $book->id }}" 
                                       :checked="isBookSelected('{{ $book->id }}')"
                                       @change="toggleBook('{{ $book->id }}')"
                                       class="mt-1 w-4 h-4 text-primary border-neutral-border rounded focus:ring-primary/20 shrink-0">
                                
                                <div class="ml-3 flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-1">
                                        <span class="font-sans text-xs font-bold text-neutral-dark group-hover:text-primary transition-colors truncate">
                                            {{ $book->title }}
                                        </span>
                                        <span class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-neutral-100 text-neutral-muted shrink-0">
                                            RPK-B{{ str_pad($book->id, 4, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-neutral-body truncate mt-0.5">Penulis: {{ $book->author }}</p>
                                    <div class="flex items-center justify-between text-[10px] text-neutral-muted mt-1.5 pt-1.5 border-t border-neutral-border/60">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3 text-neutral-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            {{ $book->location->name ?? 'Rak Utama' }}
                                        </span>
                                        <span class="font-semibold text-emerald-700">Stok: {{ $book->available_stock }} Eks</span>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('book_ids')" class="mt-1.5" />
                </div>

                <!-- Action Buttons -->
                <div class="pt-6 border-t border-neutral-border flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.loans.index') }}" class="btn-editorial-outline text-xs py-2 px-4 uppercase tracking-wider">Batal</a>
                    <x-button type="submit" variant="primary" class="text-xs py-2.5 px-6 uppercase tracking-wider font-semibold">
                        Simpan Transaksi Sirkulasi
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
