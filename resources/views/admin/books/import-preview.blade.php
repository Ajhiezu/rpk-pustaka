<x-app-layout>
    <x-slot name="header">
        Pratinjau & Validasi Import Massal, RPK PUSTAKA IMM SAINTEKMU
    </x-slot>

    <x-slot name="actions">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.books.import.create') }}" class="btn-editorial-outline text-xs py-2 px-3 shadow-xs">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Berkas
            </a>
            <button type="button" @click="startBatchImport()" class="btn-editorial text-xs py-2 px-4 font-bold uppercase tracking-wider shadow-sm flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Import Semua Naskah
            </button>
        </div>
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300" x-data="previewManager('{{ $batchId }}')">
        <!-- Summary Stats & Top Action Band -->
        <div class="bg-white p-5 rounded-xl border border-neutral-border shadow-xs space-y-4">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="p-3 bg-neutral-surface rounded-lg border border-neutral-border">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-neutral-muted block">Total Kandidat</span>
                    <span class="text-xl font-extrabold text-neutral-dark mt-1 block" x-text="totalCount"></span>
                </div>
                <div class="p-3 bg-[#EDF7ED] rounded-lg border border-[#C8E6C9]">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-success block">Siap Diimport</span>
                    <span class="text-xl font-extrabold text-success mt-1 block" x-text="validCount"></span>
                </div>
                <div class="p-3 bg-[#FFF9ED] rounded-lg border border-[#FDE68A]">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#B45309] block">Perlu Dilengkapi</span>
                    <span class="text-xl font-extrabold text-[#B45309] mt-1 block" x-text="warningCount"></span>
                </div>
                <div class="p-3 bg-[#F3E8FF] rounded-lg border border-[#E9D5FF]">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#7E22CE] block">Potensi Duplikat</span>
                    <span class="text-xl font-extrabold text-[#7E22CE] mt-1 block" x-text="duplicateCount"></span>
                </div>
            </div>

            <!-- Top Action Toolbar -->
            <div class="pt-3 border-t border-neutral-border flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-neutral-body flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-success"></span>
                    <span>Tinjau data naskah di bawah sebelum disimpan ke katalog Master:</span>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <a href="{{ route('admin.books.import.create') }}" class="btn-editorial-outline text-xs py-2 px-4 text-center w-1/2 sm:w-auto">
                        Batalkan Sesi
                    </a>
                    <button type="button" @click="startBatchImport()" class="btn-editorial text-xs py-2 px-5 font-bold uppercase tracking-wider shadow-xs w-1/2 sm:w-auto cursor-pointer flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Import Semua Naskah
                    </button>
                </div>
            </div>
        </div>

        <!-- Form Submission for Final Import -->
        <form id="importForm" action="{{ route('admin.books.import.store') }}" method="POST" @submit.prevent="startBatchImport()">
            @csrf
            <input type="hidden" name="batch_id" value="{{ $batchId }}">

            <!-- Candidate Cards List -->
            <div class="space-y-6">
                @foreach($candidates as $id => $cand)
                    <div class="bg-white rounded-xl border border-neutral-border shadow-xs overflow-hidden transition-all hover:border-primary/40"
                         id="candidate-card-{{ $id }}"
                         data-status="{{ $cand['status'] }}">
                        
                        <!-- Top Header Bar -->
                        <div class="bg-neutral-surface px-5 py-3 border-b border-neutral-border flex flex-wrap items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="font-mono font-bold text-neutral-dark">#{{ $loop->iteration }}</span>
                                <span class="text-neutral-muted">•</span>
                                <span class="font-semibold text-neutral-dark truncate max-w-xs sm:max-w-md" title="{{ $cand['original_filename'] }}">
                                    {{ $cand['original_filename'] }}
                                </span>
                                @if($cand['collection_type'] === 'digital')
                                    <span class="text-[10px] font-bold text-[#B45309] bg-[#FFF9ED] border border-[#FDE68A] px-2 py-0.5 rounded">
                                        Naskah Digital
                                    </span>
                                @else
                                    <span class="text-[10px] font-bold text-success bg-[#EDF7ED] border border-[#C8E6C9] px-2 py-0.5 rounded">
                                        Koleksi Fisik
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-3">
                                <!-- Status Badge -->
                                @if($cand['status'] === 'VALID')
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-success bg-[#EDF7ED] border border-[#C8E6C9] px-2.5 py-0.5 rounded-full">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        VALID / SIAP
                                    </span>
                                @elseif($cand['status'] === 'DUPLICATE')
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#7E22CE] bg-[#F3E8FF] border border-[#E9D5FF] px-2.5 py-0.5 rounded-full">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        DUPLIKAT
                                    </span>
                                @elseif($cand['status'] === 'ERROR')
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-danger bg-[#FDEDED] border border-[#FFCDD2] px-2.5 py-0.5 rounded-full">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        ERROR
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#B45309] bg-[#FFF9ED] border border-[#FDE68A] px-2.5 py-0.5 rounded-full">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        PERLU DILENGKAPI
                                    </span>
                                @endif

                                <!-- Delete from Queue Button -->
                                <button type="button" @click="deleteCandidate('{{ $id }}')" 
                                        class="text-neutral-muted hover:text-danger hover:bg-red-50 p-1.5 rounded transition-colors"
                                        title="Hapus naskah ini dari antrean import">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Card Body: Cover + Editable Fields -->
                        <div class="p-6 grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                            <!-- Left: Cover Preview + Replace Cover -->
                            <div class="md:col-span-3 flex flex-col items-center space-y-3">
                                <div class="w-32 h-44 bg-neutral-surface rounded-lg border border-neutral-border shadow-xs overflow-hidden flex items-center justify-center relative group">
                                    <img id="cover-img-{{ $id }}" 
                                         src="{{ $cand['cover_path'] ? asset('storage/'.$cand['cover_path']) : '' }}" 
                                         alt="Sampul {{ $cand['title'] }}" 
                                         class="w-full h-full object-cover {{ empty($cand['cover_path']) ? 'hidden' : '' }}">
                                    
                                    <div id="cover-placeholder-{{ $id }}" class="w-full h-full {{ !empty($cand['cover_path']) ? 'hidden' : 'flex' }} flex-col items-center justify-center text-neutral-muted p-2 text-center">
                                        <svg class="w-8 h-8 text-neutral-muted/60 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        <span class="text-[10px]">Tanpa Sampul</span>
                                    </div>
                                </div>

                                <input type="file" id="cover-file-input-{{ $id }}" accept="image/jpeg,image/png,image/webp" class="hidden" @change="uploadManualCover('{{ $id }}', $event)">
                                <button type="button" @click="document.getElementById('cover-file-input-{{ $id }}').click()" class="btn-editorial-outline text-[11px] py-1 px-3 w-32 text-center">
                                    {{ !empty($cand['cover_path']) ? 'Ganti Sampul' : 'Unggah Sampul' }}
                                </button>
                                
                                @if(!empty($cand['cover_path']))
                                    <span class="text-[10px] text-neutral-muted italic text-center">✓ Sampul otomatis siap</span>
                                @endif
                            </div>

                            <!-- Right: Metadata Edit Fields -->
                            <div class="md:col-span-9 space-y-4">
                                <!-- Row 1: Judul & Penulis -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-sans text-xs font-bold uppercase tracking-wider text-neutral-dark mb-1">
                                            Judul Buku <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="candidates[{{ $id }}][title]" value="{{ $cand['title'] }}" required
                                               class="w-full px-3 py-2 bg-white border border-neutral-border rounded-md text-xs text-neutral-dark font-medium focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block font-sans text-xs font-bold uppercase tracking-wider text-neutral-dark mb-1">
                                            Penulis / Pengarang <span class="text-[10px] text-neutral-muted font-normal">(Opsional)</span>
                                        </label>
                                        <input type="text" name="candidates[{{ $id }}][author]" value="{{ $cand['author'] }}" placeholder="Tanpa Penulis (Default)..."
                                               class="w-full px-3 py-2 bg-white border border-neutral-border rounded-md text-xs text-neutral-dark font-medium focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    </div>
                                </div>

                                <!-- Row 2: Kategori, Bahasa, Tahun, Halaman -->
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block font-sans text-xs font-bold uppercase tracking-wider text-neutral-dark mb-1">
                                            Kategori <span class="text-[10px] text-neutral-muted font-normal">(Opsional)</span>
                                        </label>
                                        <select name="candidates[{{ $id }}][category_id]"
                                                class="w-full px-3 py-2 bg-white border border-neutral-border rounded-md text-xs text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                            <option value="">-- Umum (Otomatis) --</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}" {{ $cand['category_id'] == $cat->id ? 'selected' : '' }}>
                                                    {{ $cat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-sans text-xs font-bold uppercase tracking-wider text-neutral-dark mb-1">
                                            Bahasa
                                        </label>
                                        <select name="candidates[{{ $id }}][language]"
                                                class="w-full px-3 py-2 bg-white border border-neutral-border rounded-md text-xs text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                            <option value="Indonesia" {{ ($cand['language'] ?? 'Indonesia') === 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                                            <option value="English" {{ ($cand['language'] ?? '') === 'English' ? 'selected' : '' }}>English</option>
                                            <option value="Arab" {{ ($cand['language'] ?? '') === 'Arab' ? 'selected' : '' }}>Arab</option>
                                            <option value="Lainnya" {{ !in_array($cand['language'] ?? '', ['Indonesia', 'English', 'Arab']) ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-sans text-xs font-bold uppercase tracking-wider text-neutral-dark mb-1">
                                            Tahun Terbit
                                        </label>
                                        <input type="number" name="candidates[{{ $id }}][year]" value="{{ $cand['year'] }}" placeholder="Contoh: 2024"
                                               class="w-full px-3 py-2 bg-white border border-neutral-border rounded-md text-xs text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block font-sans text-xs font-bold uppercase tracking-wider text-neutral-dark mb-1">
                                            Jumlah Halaman
                                        </label>
                                        <input type="number" name="candidates[{{ $id }}][page_count]" value="{{ $cand['page_count'] }}" placeholder="Total hal."
                                               class="w-full px-3 py-2 bg-white border border-neutral-border rounded-md text-xs text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    </div>
                                </div>

                                <!-- Row 3: Jenis Karya, Penerbit, & ISBN -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-sans text-xs font-bold uppercase tracking-wider text-neutral-dark mb-1">
                                            Jenis Karya
                                        </label>
                                            <select name="candidates[{{ $id }}][book_type]"
                                                    class="w-full px-3 py-2 bg-white border border-neutral-border rounded-md text-xs text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                <option value="fiksi" {{ ($cand['book_type'] ?? 'fiksi') === 'fiksi' ? 'selected' : '' }}>Fiksi (Novel)</option>
                                                <option value="nonfiksi" {{ ($cand['book_type'] ?? '') === 'nonfiksi' ? 'selected' : '' }}>Non-Fiksi</option>
                                            </select>
                                    </div>
                                    <div>
                                        <label class="block font-sans text-xs font-bold uppercase tracking-wider text-neutral-dark mb-1">
                                            Penerbit
                                        </label>
                                        <input type="text" name="candidates[{{ $id }}][publisher]" value="{{ $cand['publisher'] }}" placeholder="Nama penerbit..."
                                               class="w-full px-3 py-2 bg-white border border-neutral-border rounded-md text-xs text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block font-sans text-xs font-bold uppercase tracking-wider text-neutral-dark mb-1">
                                            Nomor ISBN
                                        </label>
                                        <input type="text" name="candidates[{{ $id }}][isbn]" value="{{ $cand['isbn'] }}" placeholder="Contoh: 9786020298032"
                                               inputmode="numeric" pattern="[0-9]*" maxlength="13"
                                               oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                               class="w-full px-3 py-2 bg-white border border-neutral-border rounded-md text-xs text-neutral-dark font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    </div>
                                </div>

                                <!-- Extra Physical fields if collection_type is fisik -->
                                @if($cand['collection_type'] === 'fisik')
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-3 bg-neutral-surface rounded border border-neutral-border">
                                        <div>
                                            <label class="block font-sans text-xs font-bold uppercase tracking-wider text-neutral-dark mb-1">
                                                Stok Fisik (Eksemplar) <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" name="candidates[{{ $id }}][stock]" value="{{ $cand['stock'] ?? 1 }}" min="1" required
                                                   class="w-full px-3 py-2 bg-white border border-neutral-border rounded-md text-xs text-neutral-dark font-bold focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                        </div>
                                        <div>
                                            <label class="block font-sans text-xs font-bold uppercase tracking-wider text-neutral-dark mb-1">
                                                Lokasi Rak Buku <span class="text-[10px] text-neutral-muted font-normal">(Opsional)</span>
                                            </label>
                                            <select name="candidates[{{ $id }}][location_id]"
                                                    class="w-full px-3 py-2 bg-white border border-neutral-border rounded-md text-xs text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                <option value="">-- Belum Ditentukan (Opsional) --</option>
                                                @foreach($locations as $loc)
                                                    <option value="{{ $loc->id }}" {{ ($cand['location_id'] ?? null) == $loc->id ? 'selected' : '' }}>
                                                        {{ $loc->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                <!-- Row 4: Sinopsis / Deskripsi -->
                                <div>
                                    <label class="block font-sans text-xs font-bold uppercase tracking-wider text-neutral-dark mb-1">
                                        Sinopsis / Deskripsi Ringkas
                                    </label>
                                    <textarea name="candidates[{{ $id }}][description]" rows="2" placeholder="Sinopsis singkat naskah buku..."
                                              class="w-full px-3 py-2 bg-white border border-neutral-border rounded-md text-xs text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ $cand['description'] }}</textarea>
                                </div>

                                <!-- Feedback messages if any -->
                                @if(!empty($cand['status_messages']))
                                    <div class="space-y-1 pt-1">
                                        @foreach($cand['status_messages'] as $msg)
                                            <p class="text-[11px] text-[#B45309] flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span>{{ $msg }}</span>
                                            </p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Sticky Bottom Action Bar -->
            <div class="sticky bottom-4 z-20 mt-8 bg-white/95 backdrop-blur border border-neutral-border p-4 rounded-xl shadow-lg flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-neutral-dark flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-success"></span>
                    <span>Siap mengimport <strong class="font-bold text-neutral-dark" x-text="totalCount"></strong> koleksi buku ke katalog Master</span>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <a href="{{ route('admin.books.import.create') }}" class="btn-editorial-outline text-xs py-2.5 px-4 text-center w-1/2 sm:w-auto">
                        Batalkan Sesi
                    </a>
                    <x-button type="submit" variant="primary" class="w-1/2 sm:w-auto px-6 py-2.5 text-xs uppercase tracking-wider font-semibold shadow-xs">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Import Semua Naskah
                    </x-button>
                </div>
            </div>
        </form>

        <!-- Chunked Import Progress Modal Overlay -->
        <div x-show="isImporting" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4" style="display: none;">
            <div class="bg-white rounded-2xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-neutral-border space-y-5 text-center">
                <div class="w-16 h-16 rounded-full bg-primary/10 text-primary flex items-center justify-center mx-auto animate-pulse">
                    <svg class="w-8 h-8 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-neutral-dark">Memproses Import Massal</h3>
                    <p class="text-xs text-neutral-body mt-1" x-text="statusText"></p>
                </div>
                <!-- Progress Bar -->
                <div class="space-y-2">
                    <div class="w-full bg-neutral-surface rounded-full h-3 overflow-hidden border border-neutral-border">
                        <div class="bg-primary h-full transition-all duration-300 rounded-full" :style="`width: ${importProgress}%`"></div>
                    </div>
                    <div class="flex justify-between text-xs font-semibold text-neutral-muted">
                        <span x-text="`${processedCount} / ${totalCount} Naskah`"></span>
                        <span x-text="`${importProgress}%`"></span>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 pt-2 text-center text-xs">
                    <div class="p-2 bg-[#EDF7ED] rounded border border-[#C8E6C9]">
                        <span class="block text-[10px] uppercase font-bold text-success">Berhasil</span>
                        <span class="font-extrabold text-success text-sm" x-text="importedSuccess"></span>
                    </div>
                    <div class="p-2 bg-[#F3E8FF] rounded border border-[#E9D5FF]">
                        <span class="block text-[10px] uppercase font-bold text-[#7E22CE]">Duplikat</span>
                        <span class="font-extrabold text-[#7E22CE] text-sm" x-text="importedDuplicate"></span>
                    </div>
                    <div class="p-2 bg-[#FDEDED] rounded border border-[#FFCDD2]">
                        <span class="block text-[10px] uppercase font-bold text-danger">Gagal</span>
                        <span class="font-extrabold text-danger text-sm" x-text="importedFailed"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewManager(batchId) {
            return {
                batchId: batchId,
                totalCount: {{ count($candidates) }},
                validCount: {{ count(array_filter($candidates, fn($c) => $c['status'] === 'VALID')) }},
                warningCount: {{ count(array_filter($candidates, fn($c) => in_array($c['status'], ['WARNING', 'ERROR']))) }},
                duplicateCount: {{ count(array_filter($candidates, fn($c) => $c['status'] === 'DUPLICATE')) }},

                isImporting: false,
                importProgress: 0,
                processedCount: 0,
                importedSuccess: 0,
                importedDuplicate: 0,
                importedFailed: 0,
                statusText: 'Mempersiapkan import massal...',

                async startBatchImport() {
                    if (this.isImporting) return;
                    
                    const form = document.getElementById('importForm');
                    const candidateIds = @json(array_values(array_keys($candidates)));
                    const total = candidateIds.length;
                    const chunkSize = 5;

                    if (total === 0) {
                        alert('Tidak ada kandidat untuk diimport.');
                        return;
                    }

                    this.isImporting = true;
                    this.importProgress = 0;
                    this.processedCount = 0;
                    this.importedSuccess = 0;
                    this.importedDuplicate = 0;
                    this.importedFailed = 0;

                    for (let i = 0; i < total; i += chunkSize) {
                        const chunkIds = candidateIds.slice(i, i + chunkSize);
                        const isLastChunk = (i + chunkSize) >= total;
                        
                        this.statusText = `Mengimport naskah ${i + 1} s.d. ${Math.min(i + chunkSize, total)} dari ${total}...`;

                        const reqData = new FormData(form);
                        reqData.delete('selected_ids[]');
                        reqData.delete('selected_ids');
                        chunkIds.forEach(id => reqData.append('selected_ids[]', id));
                        reqData.append('is_chunk', '1');
                        reqData.append('is_last_chunk', isLastChunk ? '1' : '0');

                        try {
                            const res = await fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                },
                                body: reqData
                            });

                            const data = await res.json();
                            if (data.success) {
                                this.importedSuccess += (data.imported || 0);
                                this.importedDuplicate += (data.duplicate || 0);
                                this.importedFailed += (data.failed || 0);
                            } else {
                                this.importedFailed += chunkIds.length;
                            }
                        } catch (err) {
                            console.error('Chunk import error:', err);
                            this.importedFailed += chunkIds.length;
                        }

                        this.processedCount = Math.min(i + chunkSize, total);
                        this.importProgress = Math.round((this.processedCount / total) * 100);
                    }

                    this.statusText = 'Proses import selesai!';
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Import Massal Selesai!',
                        html: `<p class="text-xs text-neutral-body leading-relaxed">Proses import selesai! Total: <strong>${total}</strong> naskah<br>• Berhasil: <strong>${this.importedSuccess}</strong><br>• Duplikat dilewati: <strong>${this.importedDuplicate}</strong><br>• Gagal: <strong>${this.importedFailed}</strong></p>`,
                        confirmButtonColor: '#C62828',
                        background: '#FFFFFF',
                    }).then(() => {
                        window.location.href = '{{ route("admin.books.index") }}';
                    });
                },

                async uploadManualCover(candidateId, event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('batch_id', this.batchId);
                    formData.append('candidate_id', candidateId);
                    formData.append('cover_image', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    try {
                        const response = await fetch('{{ route("admin.books.import.cover") }}', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            body: formData,
                        });

                        const res = await response.json();
                        if (res.success && res.cover_url) {
                            const imgEl = document.getElementById(`cover-img-${candidateId}`);
                            const placeholderEl = document.getElementById(`cover-placeholder-${candidateId}`);
                            imgEl.src = res.cover_url;
                            imgEl.classList.remove('hidden');
                            placeholderEl.classList.add('hidden');
                            placeholderEl.classList.remove('flex');
                        } else {
                            alert(res.message || 'Gagal mengunggah sampul buku.');
                        }
                    } catch (e) {
                        console.error('Upload cover error:', e);
                        alert('Terjadi kesalahan saat mengunggah sampul buku.');
                    }
                },

                async deleteCandidate(candidateId) {
                    if (!confirm('Hapus naskah ini dari antrean import?')) return;

                    const formData = new FormData();
                    formData.append('batch_id', this.batchId);
                    formData.append('candidate_id', candidateId);
                    formData.append('_token', '{{ csrf_token() }}');

                    try {
                        const response = await fetch('{{ route("admin.books.import.remove") }}', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            body: formData,
                        });

                        const res = await response.json();
                        if (res.success) {
                            const card = document.getElementById(`candidate-card-${candidateId}`);
                            if (card) {
                                card.remove();
                                this.totalCount = res.remaining;
                                if (res.remaining === 0) {
                                    window.location.href = '{{ route("admin.books.import.create") }}';
                                }
                            }
                        }
                    } catch (e) {
                        console.error('Delete candidate error:', e);
                    }
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
