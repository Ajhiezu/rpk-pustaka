<x-app-layout>
    <x-slot name="header">
        Perbarui Kategori Buku
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
            <x-slot name="header">Formulir Pembaruan Kategori</x-slot>

            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Category Info Header Banner -->
                <div class="flex items-center space-x-4 p-4 bg-neutral-surface rounded-md border border-neutral-border">
                    <div class="w-12 h-12 bg-primary-light text-primary border border-primary/20 rounded-md flex items-center justify-center font-bold text-sm shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-neutral-dark">{{ $category->name }}</h3>
                        <p class="text-xs text-neutral-body font-mono mt-0.5">Slug: {{ $category->slug }}</p>
                        <x-badge variant="indigo" class="mt-1.5">
                            {{ $category->books()->count() }} Buku Terhubung
                        </x-badge>
                    </div>
                </div>

                <div class="space-y-4">
                    <x-input 
                        label="Nama Kategori" 
                        name="name" 
                        :value="old('name', $category->name)" 
                        required 
                        :error="($errors ?? null)?->first('name')" 
                    />

                    <div class="p-3.5 bg-neutral-surface rounded-md border border-neutral-border text-xs text-neutral-body space-y-1">
                        <div class="flex items-center gap-1.5 font-semibold text-neutral-dark">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Keterangan Pembaruan
                        </div>
                        <p class="leading-relaxed">
                            Mengubah nama kategori akan secara otomatis memperbarui slug dan keterkaitan filter pada seluruh buku yang bernaung di bawah kategori ini.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-neutral-border">
                    <a href="{{ route('admin.categories.index') }}" class="btn-editorial-outline text-xs uppercase tracking-wider font-semibold py-2.5 px-5">
                        Batal
                    </a>
                    <x-button type="submit" variant="primary" class="text-xs uppercase tracking-wider font-semibold py-2.5 px-6">
                        Perbarui Kategori
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
