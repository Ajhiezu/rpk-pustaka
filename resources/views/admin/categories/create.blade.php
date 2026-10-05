<x-app-layout>
    <x-slot name="header">
        Tambah Kategori Baru
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300">
        <div>
            <a href="{{ route('admin.categories.index') }}" 
               class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-neutral-body hover:text-primary transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Manajemen Kategori
            </a>
        </div>

        <x-card>
            <x-slot name="header">Formulir Kategori Buku</x-slot>

            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="space-y-4">
                    <x-input 
                        label="Nama Kategori" 
                        name="name" 
                        :value="old('name')" 
                        placeholder="Contoh: Filsafat, Sains Data, Sejarah Nusantara" 
                        required 
                        :error="($errors ?? null)?->first('name')" 
                    />

                    <div class="p-3.5 bg-neutral-surface rounded-md border border-neutral-border text-xs text-neutral-body space-y-1">
                        <div class="flex items-center gap-1.5 font-semibold text-neutral-dark">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Informasi Sistem
                        </div>
                        <p class="leading-relaxed">
                            Slug dan rujukan pencarian akan dibuat secara otomatis berdasarkan nama kategori untuk mempermudah filter buku di katalog publik & anggota.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-neutral-border">
                    <a href="{{ route('admin.categories.index') }}" class="btn-editorial-outline text-xs uppercase tracking-wider font-semibold py-2.5 px-5">
                        Batal
                    </a>
                    <x-button type="submit" variant="primary" class="text-xs uppercase tracking-wider font-semibold py-2.5 px-6">
                        Simpan Kategori
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
