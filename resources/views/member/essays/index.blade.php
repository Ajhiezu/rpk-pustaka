<x-app-layout>
    <x-slot name="header">
        Ruang Karya & Esai Anggota, RPK PUSTAKA IMM SAINTEKMU
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('anggota.essays.create') }}" class="btn-editorial text-xs py-2 px-4 shadow-xs">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Ajukan Karya Tulis Baru
        </a>
    </x-slot>

    <div class="space-y-10 animate-in fade-in duration-300">
        <!-- Lead -->
        <div class="space-y-1.5">
            <span class="text-xs uppercase tracking-[0.2em] font-semibold text-primary block">Wadah Literasi Anggota</span>
            <h2 class="font-serif text-2xl sm:text-3xl font-normal text-neutral-dark tracking-tight">Karya Tulis & Esai Anda</h2>
            <p class="text-xs sm:text-sm text-neutral-body">Sampaikan gagasan, telaah pustaka, dan artikel ilmiah Anda untuk ditinjau oleh kurator RPK PUSTAKA IMM SAINTEKMU.</p>
        </div>

        <!-- My Essays Table -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-serif text-lg font-semibold text-neutral-dark">Daftar Pengajuan Naskah Saya</h3>
                <span class="text-xs text-neutral-muted">Total: {{ $myEssays->total() }} Naskah</span>
            </div>

            <!-- ============================================== -->
            <!-- MOBILE CARD VIEW (Tampil khusus layar HP/Tablet) -->
            <!-- ============================================== -->
            <div class="block md:hidden space-y-3">
                @forelse($myEssays as $essay)
                    <div class="bg-white rounded-lg border border-neutral-border shadow-xs overflow-hidden">
                        <div class="p-4 space-y-2.5">
                            <div class="flex items-center justify-between gap-2">
                                <div>
                                    @if($essay->isOnline())
                                        <span class="text-[10px] font-semibold text-primary bg-primary-light px-2 py-0.5 rounded border border-red-200">
                                            Tulis Online
                                        </span>
                                    @else
                                        <span class="text-[10px] font-semibold text-[#B45309] bg-[#FFF9ED] px-2 py-0.5 rounded border border-[#FDE68A]">
                                            Berkas {{ strtoupper($essay->file_type ?? 'Dokumen') }}
                                        </span>
                                    @endif
                                    <span class="text-[10px] text-neutral-muted ml-1.5">{{ $essay->created_at->format('d M Y') }}</span>
                                </div>
                                <div class="shrink-0">
                                    @if($essay->status === 'submitted')
                                        <x-badge variant="indigo">Menunggu</x-badge>
                                    @elseif($essay->status === 'approved')
                                        <x-badge variant="emerald">Disetujui</x-badge>
                                    @elseif($essay->status === 'published')
                                        <x-badge variant="primary">Published</x-badge>
                                    @elseif($essay->status === 'revision')
                                        <x-badge variant="amber">Revisi</x-badge>
                                    @elseif($essay->status === 'rejected')
                                        <x-badge variant="rose">Ditolak</x-badge>
                                    @endif
                                </div>
                            </div>

                            <a href="{{ route('anggota.essays.show', $essay) }}" class="font-serif text-sm font-bold text-neutral-dark hover:text-primary transition-colors block leading-snug line-clamp-2">
                                {{ $essay->title }}
                            </a>

                            @if($essay->review_note)
                                <div class="bg-neutral-surface p-2.5 rounded border border-neutral-border/60 text-xs">
                                    <span class="text-[10px] text-neutral-muted block font-semibold">Catatan Evaluasi:</span>
                                    <span class="italic text-neutral-body line-clamp-2">"{{ $essay->review_note }}"</span>
                                </div>
                            @endif
                        </div>

                        <!-- Bottom Action Bar -->
                        <div class="bg-neutral-surface px-3.5 py-2.5 border-t border-neutral-border flex flex-wrap items-center justify-between gap-2">
                            <a href="{{ route('anggota.essays.show', $essay) }}" class="btn-editorial text-xs py-1.5 px-3.5 uppercase tracking-wider font-semibold inline-flex items-center gap-1 shadow-xs">
                                Buka Lembar Naskah &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 rounded-lg border border-neutral-border text-center">
                        <p class="font-serif text-base font-semibold text-neutral-dark">Anda Belum Mengirimkan Karya Tulis</p>
                        <p class="text-xs text-neutral-muted mt-1">Tuangkan pemikiran kritis Anda dalam bentuk esai atau unggah naskah ilmiah Anda.</p>
                        <div class="mt-4">
                            <a href="{{ route('anggota.essays.create') }}" class="btn-editorial text-xs py-2 px-5">
                                Ajukan Tulisan Pertama
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- ============================================== -->
            <!-- DESKTOP TABLE VIEW (Tampil di Laptop & PC)      -->
            <!-- ============================================== -->
            <div class="hidden md:block">
                <x-table :headers="['Judul Naskah', 'Metode Pengajuan', 'Tanggal Diajukan', 'Status Kurasi', 'Catatan Evaluasi', 'Aksi']">
                    @forelse($myEssays as $essay)
                        <tr class="group transition-colors hover:bg-neutral-surface">
                            <td class="px-6 py-4">
                                <a href="{{ route('anggota.essays.show', $essay) }}" class="font-serif text-sm font-semibold text-neutral-dark hover:text-primary transition-colors block line-clamp-1">
                                    {{ $essay->title }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($essay->isOnline())
                                    <span class="text-[11px] font-semibold text-primary bg-primary-light px-2.5 py-0.5 rounded border border-red-200">
                                        Tulis Online
                                    </span>
                                @else
                                    <span class="text-[11px] font-semibold text-[#B45309] bg-[#FFF9ED] px-2.5 py-0.5 rounded border border-[#FDE68A]">
                                        Berkas {{ strtoupper($essay->file_type ?? 'Dokumen') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-neutral-muted">
                                {{ $essay->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($essay->status === 'submitted')
                                    <x-badge variant="indigo">Menunggu Review</x-badge>
                                @elseif($essay->status === 'approved')
                                    <x-badge variant="emerald">Disetujui</x-badge>
                                @elseif($essay->status === 'published')
                                    <x-badge variant="primary">Published</x-badge>
                                @elseif($essay->status === 'revision')
                                    <x-badge variant="amber">Perlu Revisi</x-badge>
                                @elseif($essay->status === 'rejected')
                                    <x-badge variant="rose">Ditolak</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-neutral-dark max-w-xs">
                                @if($essay->review_note)
                                    <span class="line-clamp-1 italic text-neutral-body" title="{{ $essay->review_note }}">
                                        "{{ $essay->review_note }}"
                                    </span>
                                @else
                                    <span class="text-neutral-muted italic">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('anggota.essays.show', $essay) }}" class="btn-editorial-outline text-xs py-1.5 px-3 uppercase tracking-wider font-semibold inline-flex items-center gap-1">
                                    Buka Lembar &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <p class="font-serif text-base font-semibold text-neutral-dark">Anda Belum Mengirimkan Karya Tulis</p>
                                <p class="text-xs text-neutral-muted mt-1">Tuangkan pemikiran kritis Anda dalam bentuk esai atau unggah naskah ilmiah Anda.</p>
                                <div class="mt-4">
                                    <a href="{{ route('anggota.essays.create') }}" class="btn-editorial text-xs py-2 px-5">
                                        Ajukan Tulisan Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </x-table>
            </div>

            <div class="pt-2">
                {{ $myEssays->links() }}
            </div>
        </div>

        <!-- Published Community Essays Section -->
        @if(isset($publishedEssays) && $publishedEssays->count() > 0)
            <div class="space-y-4 pt-6 border-t border-neutral-border">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary">Karya Komunitas Terbit</span>
                    <h3 class="font-serif text-xl font-semibold text-neutral-dark">Esai Pilihan Anggota Lainnya</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($publishedEssays as $pub)
                        <div class="bg-white p-5 rounded-lg border border-neutral-border hover:border-primary/50 transition-all flex flex-col justify-between">
                            <div class="space-y-2.5">
                                <span class="text-[10px] text-neutral-muted block">{{ $pub->created_at->format('d M Y') }} • Oleh {{ $pub->user->name ?? 'Anggota' }}</span>
                                <h4 class="font-serif text-base font-semibold text-neutral-dark line-clamp-2">
                                    {{ $pub->title }}
                                </h4>
                                @if($pub->isOnline())
                                    <p class="text-xs text-neutral-body line-clamp-3 leading-relaxed">
                                        {{ Str::limit(strip_tags($pub->content), 120) }}
                                    </p>
                                @else
                                    <p class="text-xs text-neutral-muted italic">Naskah dokumen unggahan resmi yang telah dikurasi.</p>
                                @endif
                            </div>
                            <div class="pt-4 border-t border-neutral-border mt-3 flex items-center justify-between">
                                <span class="text-[10px] uppercase tracking-wider font-semibold text-success">✓ Terkurasi</span>
                                <a href="{{ route('anggota.essays.show', $pub) }}" class="text-xs font-semibold text-primary hover:underline">
                                    Baca Karya &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
