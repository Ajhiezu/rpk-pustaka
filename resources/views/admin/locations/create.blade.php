<x-app-layout>
    <x-slot name="header">
        Tambah Lokasi Rak Baru
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300">
        <div>
            <a href="{{ route('admin.locations.index') }}" 
               class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-neutral-body hover:text-primary transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Manajemen Lokasi Rak
            </a>
        </div>

        <x-card>
            <x-slot name="header">Formulir Lokasi Rak Simpan</x-slot>

            <form action="{{ route('admin.locations.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="space-y-5">
                    <x-input 
                        label="Nama / Kode Rak" 
                        name="name" 
                        :value="old('name')" 
                        placeholder="Contoh: Rak Utama A-01, Lemari Referensi B" 
                        required 
                        :error="($errors ?? null)?->first('name')" 
                    />

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider px-0.5">
                            Keterangan Posisi (Opsional)
                        </label>
                        <textarea 
                            name="description" 
                            rows="3" 
                            placeholder="Contoh: Sayap Barat Lantai 2, Baris ke-3 rak tengah" 
                            class="w-full px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark placeholder:text-neutral-muted focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs transition-all">{{ old('description') }}</textarea>
                        <x-input-error :messages="($errors ?? null)?->get('description')" class="mt-1" />
                    </div>

                    <div class="p-3.5 bg-neutral-surface rounded-md border border-neutral-border text-xs text-neutral-body space-y-1">
                        <div class="flex items-center gap-1.5 font-semibold text-neutral-dark">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Penataan Koleksi Fisik
                        </div>
                        <p class="leading-relaxed">
                            Penamaan rak yang jelas memudahkan staf pustaka dan anggota menemukan lokasi buku fisik saat layanan sirkulasi atau membaca di tempat.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-neutral-border">
                    <a href="{{ route('admin.locations.index') }}" class="btn-editorial-outline text-xs uppercase tracking-wider font-semibold py-2.5 px-5">
                        Batal
                    </a>
                    <x-button type="submit" variant="primary" class="text-xs uppercase tracking-wider font-semibold py-2.5 px-6">
                        Simpan Lokasi Rak
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
