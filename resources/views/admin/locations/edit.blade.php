<x-app-layout>
    <x-slot name="header">
        Perbarui Lokasi Rak
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
            <x-slot name="header">Formulir Pembaruan Rak Simpan</x-slot>

            <form action="{{ route('admin.locations.update', $location) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Location Info Header Banner -->
                <div class="flex items-center space-x-4 p-4 bg-neutral-surface rounded-md border border-neutral-border">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md flex items-center justify-center font-bold text-sm shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-neutral-dark">{{ $location->name }}</h3>
                        <p class="text-xs text-neutral-body mt-0.5">{{ $location->description ?: 'Tidak ada keterangan posisi' }}</p>
                        <x-badge variant="emerald" class="mt-1.5">
                            {{ $location->books()->count() }} Buku Tersimpan di Rak Ini
                        </x-badge>
                    </div>
                </div>

                <div class="space-y-5">
                    <x-input 
                        label="Nama / Kode Rak" 
                        name="name" 
                        :value="old('name', $location->name)" 
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
                            class="w-full px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark placeholder:text-neutral-muted focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs transition-all">{{ old('description', $location->description) }}</textarea>
                        <x-input-error :messages="($errors ?? null)?->get('description')" class="mt-1" />
                    </div>

                    <div class="p-3.5 bg-neutral-surface rounded-md border border-neutral-border text-xs text-neutral-body space-y-1">
                        <div class="flex items-center gap-1.5 font-semibold text-neutral-dark">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Keterangan Pembaruan
                        </div>
                        <p class="leading-relaxed">
                            Pembaruan informasi rak otomatis tersinkronisasi dengan seluruh buku fisik yang ditempatkan pada rak ini.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-neutral-border">
                    <a href="{{ route('admin.locations.index') }}" class="btn-editorial-outline text-xs uppercase tracking-wider font-semibold py-2.5 px-5">
                        Batal
                    </a>
                    <x-button type="submit" variant="primary" class="text-xs uppercase tracking-wider font-semibold py-2.5 px-6">
                        Perbarui Lokasi Rak
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
