<x-app-layout>
    <x-slot name="header">
        Artikel & Wawasan Pustaka — RPK PUSTAKA IMM SAINTEKMU
    </x-slot>

    <div class="space-y-10 animate-in fade-in duration-300">
        <!-- Lead -->
        <div class="space-y-1.5">
            <span class="text-xs uppercase tracking-[0.2em] font-semibold text-primary block">Wacana & Esai Kuratorial</span>
            <h2 class="font-serif text-2xl sm:text-3xl font-normal text-neutral-dark tracking-tight">Koleksi Artikel Pustaka</h2>
            <p class="text-xs sm:text-sm text-neutral-body">Kumpulan artikel telaah ilmiah, ulasan literatur, dan wawasan kuratorial yang diterbitkan oleh kurator RPK PUSTAKA IMM SAINTEKMU.</p>
        </div>

        <!-- Search Bar -->
        <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs">
            <form action="{{ route('anggota.articles.index') }}" method="GET" class="flex gap-3">
                <div class="flex-1 relative">
                    <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari artikel berdasarkan judul atau intisari..." 
                        class="w-full pl-10 pr-4 py-2 bg-neutral-surface border border-neutral-border rounded-md text-sm text-neutral-dark focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>
                <x-button type="submit" variant="primary" class="px-5 text-xs uppercase tracking-wider font-semibold">Cari</x-button>
                @if(request('search'))
                    <a href="{{ route('anggota.articles.index') }}" class="btn-editorial-outline px-4 text-xs uppercase tracking-wider font-semibold">Reset</a>
                @endif
            </form>
        </div>

        <!-- Articles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($articles as $article)
                <article class="bg-white rounded-lg border border-neutral-border overflow-hidden hover:border-primary/50 hover:shadow-md transition-all flex flex-col justify-between group">
                    <div>
                        @if($article->cover_image)
                            <div class="aspect-[16/9] w-full overflow-hidden bg-neutral-surface border-b border-neutral-border">
                                <img src="{{ asset('storage/'.$article->cover_image) }}" alt="{{ $article->title }}" 
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103">
                            </div>
                        @else
                            <div class="aspect-[16/9] w-full bg-neutral-surface flex items-center justify-center text-primary border-b border-neutral-border">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                            </div>
                        @endif

                        <div class="p-6 space-y-3">
                            <div class="flex items-center gap-2 text-[11px] text-neutral-muted">
                                <span>{{ $article->published_at ? $article->published_at->format('d M Y') : '' }}</span>
                                <span>•</span>
                                <span class="font-medium text-neutral-dark">{{ $article->user->name ?? 'Dewan Redaksi' }}</span>
                            </div>

                            <h3 class="font-serif text-lg font-semibold text-neutral-dark leading-snug group-hover:text-primary transition-colors line-clamp-2">
                                <a href="{{ route('anggota.articles.show', $article) }}">
                                    {{ $article->title }}
                                </a>
                            </h3>

                            <p class="text-xs text-neutral-body line-clamp-3 leading-relaxed">
                                {{ $article->excerpt ?: Str::limit(strip_tags($article->content), 140) }}
                            </p>
                        </div>
                    </div>

                    <div class="px-6 pb-6 pt-2 border-t border-neutral-border flex items-center justify-between">
                        <span class="text-[11px] font-semibold text-neutral-muted uppercase tracking-wider">Artikel Ilmiah</span>
                        <a href="{{ route('anggota.articles.show', $article) }}" class="text-xs font-semibold text-primary hover:underline">
                            Baca Selengkapnya &rarr;
                        </a>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded border border-neutral-border">
                    <p class="font-serif text-base font-semibold text-neutral-dark">Belum Ada Artikel yang Diterbitkan</p>
                    <p class="text-xs text-neutral-muted mt-1">Artikel telaah literatur akan segera hadir untuk memperkaya khazanah pustaka Anda.</p>
                </div>
            @endforelse
        </div>

        <div class="pt-4">
            {{ $articles->links() }}
        </div>
    </div>
</x-app-layout>
