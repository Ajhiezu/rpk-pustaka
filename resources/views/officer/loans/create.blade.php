<x-app-layout>
    <x-slot name="header">
        Pencatatan Peminjaman Koleksi
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6 animate-in fade-in duration-300">
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
                
                <div>
                    <label for="user_id" class="block text-xs font-semibold text-neutral-body uppercase tracking-wider mb-2 px-0.5">
                        Pilih Anggota Peminjam
                    </label>
                    <select name="user_id" id="user_id" required
                        class="w-full px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                        <option value="">-- Pilih Anggota / Peminjam Terdaftar --</option>
                        @foreach($borrowers as $borrower)
                            <option value="{{ $borrower->id }}" {{ old('user_id') == $borrower->id ? 'selected' : '' }}>
                                {{ $borrower->name }} ({{ $borrower->email }}) — [{{ strtoupper($borrower->role) }}]
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('user_id')" class="mt-1.5" />
                </div>

                <div class="p-5 bg-[#F8F8F7] rounded-md border border-neutral-border space-y-2">
                    <label for="due_date" class="block text-xs font-semibold text-neutral-body uppercase tracking-wider px-0.5">
                        Batas Waktu Pengembalian (Jatuh Tempo)
                    </label>
                    <input type="date" name="due_date" id="due_date" required 
                        min="{{ now()->addDay()->toDateString() }}" 
                        max="{{ now()->addDays(14)->toDateString() }}"
                        value="{{ now()->addDays(7)->toDateString() }}"
                        class="w-full px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-sm font-medium text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                    <p class="text-[11px] text-neutral-muted italic px-0.5">* Standar peminjaman adalah 7 hari kalender (maksimal 14 hari).</p>
                    <x-input-error :messages="$errors->get('due_date')" class="mt-1.5" />
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-neutral-body uppercase tracking-wider px-0.5">
                        Pilih Koleksi Buku yang Dipinjam
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-80 overflow-y-auto p-4 bg-[#F8F8F7] rounded-md border border-neutral-border custom-scrollbar">
                        @foreach($books as $book)
                            <label class="relative flex items-center p-3.5 bg-white rounded border border-neutral-border cursor-pointer hover:border-primary transition-all group">
                                <input type="checkbox" name="book_ids[]" value="{{ $book->id }}" 
                                    class="w-4 h-4 text-primary border-neutral-border rounded focus:ring-primary/20">
                                <span class="ml-3 flex flex-col min-w-0">
                                    <span class="font-sans text-xs font-semibold text-neutral-dark group-hover:text-primary transition-colors truncate">{{ $book->title }}</span>
                                    <span class="text-[10px] text-neutral-muted">Tersedia: {{ $book->available_stock }} Eks • {{ $book->location->name ?? 'Rak Utama' }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('book_ids')" class="mt-1.5" />
                </div>

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
