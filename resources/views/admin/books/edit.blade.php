<x-app-layout>
    <x-slot name="header">
        Perbarui Data Koleksi Master
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300">
        <div>
            <a href="{{ route('admin.books.index') }}" 
               class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-neutral-body hover:text-primary transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Daftar Buku
            </a>
        </div>

        <x-card>
            <x-slot name="header">Pembaruan Informasi Naskah & Sirkulasi</x-slot>

            <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data" 
                  x-data="{ collectionType: '{{ old('collection_type', $book->collection_type ?? 'fisik') }}' }" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Current Cover Preview -->
                <div class="flex items-center space-x-5 bg-neutral-surface p-5 rounded-md border border-neutral-border">
                    <div class="w-20 h-28 bg-neutral-surface rounded overflow-hidden shrink-0 border border-neutral-border shadow-xs">
                        @if($book->image)
                            <img src="{{ asset('storage/'.$book->image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-primary">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block mb-1">Sampul Saat Ini</span>
                        <h3 class="font-sans text-lg font-bold text-neutral-dark leading-snug truncate max-w-lg">{{ $book->title }}</h3>
                        <p class="text-xs text-neutral-body mt-0.5">{{ $book->author }} • <span class="font-mono text-[11px] font-semibold text-primary">{{ $book->book_code ?? 'TANPA-KODE' }}</span></p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="px-2 py-0.5 bg-white text-primary text-[10px] font-bold uppercase tracking-wider rounded border border-neutral-border">
                                {{ $book->category->name ?? 'Umum' }}
                            </span>
                            <span class="px-2 py-0.5 bg-neutral-dark text-white text-[10px] font-bold uppercase tracking-wider rounded">
                                Format: {{ $book->format_label }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- SEKSI 1: INFORMASI BUKU (METADATA BIBLIOGRAFI) -->
                <!-- ============================================== -->
                <div class="space-y-4">
                    <div class="pb-2 border-b border-neutral-border">
                        <h3 class="text-sm font-bold text-neutral-dark uppercase tracking-wider">1. Informasi Bibliografi Buku</h3>
                        <p class="text-xs text-neutral-muted">Identitas pokok naskah. Kosongkan field bibliografi sekunder jika data tidak tersedia.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Book Code -->
                        <div>
                            <x-input 
                                label="Kode Buku Perpustakaan *" 
                                name="book_code" 
                                id="book_code" 
                                :value="old('book_code', $book->book_code)" 
                                required
                                placeholder="Contoh: RPK-B001"
                                :error="$errors->first('book_code')"
                            />
                            <p class="text-[11px] text-neutral-muted mt-1">* Kode unik inventaris buku perpustakaan.</p>
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider mb-2 px-0.5">Kategori Buku *</label>
                            <select name="category_id" required class="w-full px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-1.5" />
                        </div>

                        <!-- Title -->
                        <div class="md:col-span-2">
                            <x-input 
                                label="Judul Lengkap Naskah / Buku *" 
                                name="title" 
                                id="title" 
                                :value="old('title', $book->title)" 
                                required 
                                :error="$errors->first('title')"
                            />
                        </div>

                        <!-- Author -->
                        <div>
                            <x-input 
                                label="Penulis / Pengarang Utama *" 
                                name="author" 
                                id="author" 
                                :value="old('author', $book->author)" 
                                required 
                                :error="$errors->first('author')"
                            />
                        </div>

                        <!-- Publisher -->
                        <div>
                            <x-input 
                                label="Penerbit Resmi (Opsional)" 
                                name="publisher" 
                                id="publisher" 
                                :value="old('publisher', $book->publisher)" 
                                placeholder="Kosongkan jika tidak tersedia"
                                :error="$errors->first('publisher')"
                            />
                        </div>

                        <!-- Year -->
                        <div>
                            <x-input 
                                label="Tahun Terbit (Opsional)" 
                                type="number" 
                                name="year" 
                                id="year" 
                                :value="old('year', $book->year)" 
                                placeholder="Contoh: 2021"
                                :error="$errors->first('year')"
                            />
                        </div>

                        <!-- ISBN -->
                        <div>
                            <x-input 
                                label="Nomor ISBN (Opsional)" 
                                name="isbn" 
                                id="isbn" 
                                :value="old('isbn', $book->isbn)" 
                                placeholder="Kosongkan jika tidak ada ISBN"
                                :error="$errors->first('isbn')"
                            />
                        </div>

                        <!-- Language -->
                        <div>
                            <x-input 
                                label="Bahasa Naskah" 
                                name="language" 
                                id="language" 
                                :value="old('language', $book->language ?? 'Indonesia')" 
                                :error="$errors->first('language')"
                            />
                        </div>

                        <!-- Page Count -->
                        <div>
                            <x-input 
                                label="Jumlah Halaman (Opsional)" 
                                type="number" 
                                name="page_count" 
                                id="page_count" 
                                :value="old('page_count', $book->page_count)" 
                                placeholder="Contoh: 280"
                                :error="$errors->first('page_count')"
                            />
                        </div>

                        <!-- Description / Synopsis -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider mb-2 px-0.5">Sinopsis / Ringkasan Materi</label>
                            <textarea name="description" rows="3" 
                                      class="w-full px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark placeholder:text-neutral-muted focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs" 
                                      placeholder="Tuliskan ikhtisar isi buku naskah...">{{ old('description', $book->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-1.5" />
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- SEKSI 2: FORMAT & JENIS KOLEKSI                -->
                <!-- ============================================== -->
                <div class="space-y-4 pt-4 border-t border-neutral-border">
                    <div class="pb-2 border-b border-neutral-border">
                        <h3 class="text-sm font-bold text-neutral-dark uppercase tracking-wider">2. Jenis Koleksi Pustaka</h3>
                        <p class="text-xs text-neutral-muted">Tentukan format peredaran buku: Cetak Fisik, E-Book Digital, atau Fisik & Digital.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="flex items-center gap-3 p-3.5 border rounded-lg cursor-pointer transition-all"
                               :class="collectionType === 'fisik' ? 'border-primary bg-primary-light/40 shadow-xs' : 'border-neutral-border bg-white hover:bg-neutral-surface'">
                            <input type="radio" name="collection_type" value="fisik" x-model="collectionType" class="text-primary focus:ring-primary">
                            <div>
                                <span class="block text-xs font-bold text-neutral-dark uppercase tracking-wider">Buku Fisik</span>
                                <span class="block text-[11px] text-neutral-muted">Hanya tersedia cetak fisik di rak</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3.5 border rounded-lg cursor-pointer transition-all"
                               :class="collectionType === 'digital' ? 'border-primary bg-primary-light/40 shadow-xs' : 'border-neutral-border bg-white hover:bg-neutral-surface'">
                            <input type="radio" name="collection_type" value="digital" x-model="collectionType" class="text-primary focus:ring-primary">
                            <div>
                                <span class="block text-xs font-bold text-neutral-dark uppercase tracking-wider">Buku Digital</span>
                                <span class="block text-[11px] text-neutral-muted">Hanya naskah digital (PDF) via website</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3.5 border rounded-lg cursor-pointer transition-all"
                               :class="collectionType === 'fisik_digital' ? 'border-primary bg-primary-light/40 shadow-xs' : 'border-neutral-border bg-white hover:bg-neutral-surface'">
                            <input type="radio" name="collection_type" value="fisik_digital" x-model="collectionType" class="text-primary focus:ring-primary">
                            <div>
                                <span class="block text-xs font-bold text-neutral-dark uppercase tracking-wider">Fisik & Digital</span>
                                <span class="block text-[11px] text-neutral-muted">Tersedia fisik di rak dan PDF online</span>
                            </div>
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('collection_type')" class="mt-1.5" />
                </div>

                <!-- ============================================== -->
                <!-- SEKSI 3: KOLEKSI FISIK (STOK & RAK)           -->
                <!-- ============================================== -->
                <div x-show="collectionType === 'fisik' || collectionType === 'fisik_digital'" class="space-y-4 pt-4 border-t border-neutral-border">
                    <div class="pb-2 border-b border-neutral-border">
                        <h3 class="text-sm font-bold text-neutral-dark uppercase tracking-wider">3. Parameter Koleksi Fisik</h3>
                        <p class="text-xs text-neutral-muted">Pengaturan jumlah eksemplar fisik di perpustakaan, rak simpan, dan skema denda kehilangan.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Stock -->
                        <div>
                            <x-input 
                                label="Jumlah Stok Eksemplar Fisik *" 
                                type="number" 
                                name="stock" 
                                id="stock" 
                                :value="old('stock', $book->stock)" 
                                min="1"
                                placeholder="Minimal 1"
                                :error="$errors->first('stock')"
                            />
                            <p class="text-[11px] text-neutral-muted mt-1">Stok saat ini: {{ $book->available_stock }} eksemplar tersedia di rak.</p>
                        </div>

                        <!-- Location -->
                        <div>
                            <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider mb-2 px-0.5">Lokasi Rak Simpan *</label>
                            <select name="location_id" class="w-full px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                                <option value="">Pilih Rak Perpustakaan</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id', $book->location_id) == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('location_id')" class="mt-1.5" />
                        </div>

                        <!-- Price / Loss fine -->
                        <div>
                            <x-input 
                                label="Taksiran Nilai Buku Fisik (Rp)" 
                                type="number" 
                                name="price" 
                                id="price" 
                                :value="old('price', $book->price)" 
                                min="0"
                                :error="$errors->first('price')"
                            />
                            <p class="text-[11px] text-neutral-muted mt-1">* Acuan internal denda ganti rugi jika buku fisik dihilangkan anggota.</p>
                        </div>                        <!-- Fine Configuration -->
                        <div>
                            <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider mb-2 px-0.5">Skema Denda Kerusakan/Hilang</label>
                            <div class="flex gap-2">
                                <select name="fine_type" id="fine_type" class="w-2/5 px-3 py-2.5 bg-white border border-neutral-border rounded-md text-xs font-semibold text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                                    <option value="fixed" {{ old('fine_type', $book->fine_type) == 'fixed' ? 'selected' : '' }}>Tetap (Harga Asli)</option>
                                    <option value="multiplier" {{ old('fine_type', $book->fine_type) == 'multiplier' ? 'selected' : '' }}>Kelipatan (x)</option>
                                    <option value="custom" {{ old('fine_type', $book->fine_type) == 'custom' ? 'selected' : '' }}>Manual</option>
                                </select>
                                <input type="text" name="fine_value" id="fine_value" value="{{ old('fine_value', $book->fine_value) }}" 
                                    class="flex-1 px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                            </div>
                            <p id="fine_calculation_hint" class="text-[11px] text-neutral-muted mt-1.5 transition-colors"></p>
                            <x-input-error :messages="$errors->get('fine_type')" class="mt-1.5" />
                            <x-input-error :messages="$errors->get('fine_value')" class="mt-1.5" />
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- SEKSI 4: BERKAS DIGITAL & SAMPUL BUKU         -->
                <!-- ============================================== -->
                <div class="space-y-5 pt-4 border-t border-neutral-border">
                    <div class="pb-2 border-b border-neutral-border">
                        <h3 class="text-sm font-bold text-neutral-dark uppercase tracking-wider">4. Berkas Digital & Sampul</h3>
                        <p class="text-xs text-neutral-muted">Pengaturan berkas naskah PDF dan foto sampul buku.</p>
                    </div>

                    <!-- Hidden fields to preserve uploaded files across validation errors -->
                    <input type="hidden" name="temp_image" id="temp_image_input" value="{{ old('temp_image') }}">
                    <input type="hidden" name="temp_image_name" id="temp_image_name_input" value="{{ old('temp_image_name') }}">
                    <input type="hidden" name="temp_pdf" id="temp_pdf_input" value="{{ old('temp_pdf') }}">
                    <input type="hidden" name="temp_pdf_name" id="temp_pdf_name_input" value="{{ old('temp_pdf_name') }}">

                    <!-- Digital PDF File Handling -->
                    <div class="p-4 bg-primary-light/40 rounded-lg border border-red-200 space-y-3">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            <label class="block text-xs font-bold text-neutral-dark uppercase tracking-wider">
                                Naskah Digital Buku (PDF)
                            </label>
                        </div>

                        @if(old('temp_pdf'))
                            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-md text-xs text-emerald-900 flex flex-col sm:flex-row sm:items-center justify-between gap-2 shadow-xs">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Berkas Naskah PDF Baru Tersimpan: <strong class="font-mono font-bold">{{ old('temp_pdf_name', basename(old('temp_pdf'))) }}</strong></span>
                                </div>
                                <span class="text-[11px] text-emerald-700 italic">Pilih berkas baru di bawah jika ingin mengganti</span>
                            </div>
                        @elseif($book->pdf_path)
                            <div class="p-3 bg-white rounded border border-neutral-border flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-success"></span>
                                    <span class="font-semibold text-neutral-dark">Berkas Naskah PDF Tersimpan di Sistem</span>
                                </div>

                                <label class="inline-flex items-center space-x-2 text-danger hover:text-red-700 cursor-pointer text-xs font-semibold">
                                    <input type="checkbox" name="remove_pdf" value="1" class="rounded text-danger focus:ring-danger border-neutral-border">
                                    <span>Hapus Berkas PDF Naskah (Aksi Eksplisit)</span>
                                </label>
                            </div>
                            <p class="text-[11px] text-neutral-muted italic">
                                * Catatan Keamanan: Jika tipe koleksi dialihkan ke Fisik, file PDF tidak akan otomatis dihapus kecuali Anda secara eksplisit mencentang kotak di atas.
                            </p>
                        @else
                            <p class="text-xs text-neutral-muted">Belum ada berkas PDF tersimpan untuk buku ini.</p>
                        @endif

                        <div class="pt-2">
                            <label class="block text-xs font-semibold text-neutral-dark mb-1">Unggah Berkas PDF Baru / Pengganti:</label>
                            <input type="file" name="pdf_file" id="pdf_file_input" accept=".pdf" onchange="renderPdfCoverPreview(this)" class="w-full text-xs text-neutral-dark file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark cursor-pointer bg-white p-2 border border-neutral-border rounded">
                            <input type="hidden" name="auto_pdf_cover" id="auto_pdf_cover">
                            <x-input-error :messages="$errors->get('pdf_file')" class="mt-1" />
                        </div>
                    </div>

                    <!-- Image Cover -->
                    <div>
                        <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider mb-2 px-0.5">Perbarui Berkas Sampul Buku (Opsional)</label>
                        <div class="flex flex-col sm:flex-row items-center gap-4 w-full">
                            <!-- Live / Current Image Preview Container -->
                            <div id="cover_preview_wrapper" class="{{ (old('temp_image') || $book->image) ? '' : 'hidden' }} relative group w-24 h-32 rounded border border-neutral-border overflow-hidden bg-neutral-surface shrink-0 shadow-xs">
                                <img id="cover_preview" src="{{ old('temp_image') ? asset('storage/'.old('temp_image')) : ($book->image ? asset('storage/'.$book->image) : '#') }}" alt="Sampul Buku" class="w-full h-full object-cover">
                                <button type="button" id="btn_reset_cover" onclick="resetCoverImage()" class="hidden absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 opacity-80 hover:opacity-100 transition-opacity shadow-sm" title="Batalkan pilihan gambar baru">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                                <span id="cover_badge" class="absolute bottom-0 inset-x-0 {{ old('temp_image') ? 'bg-emerald-700' : ($book->image ? 'bg-[#181818]/70' : 'bg-primary/90') }} text-[9px] text-white text-center py-0.5 font-semibold">
                                    {{ old('temp_image') ? 'Sampul Baru Tersimpan' : ($book->image ? 'Sampul Aktif' : 'Pratinjau') }}
                                </span>
                            </div>

                            <!-- Dropzone Label -->
                            <label for="image_input" id="drop_zone" class="flex flex-col items-center justify-center flex-1 w-full h-32 border-2 border-neutral-border border-dashed rounded-md cursor-pointer bg-neutral-surface hover:bg-neutral-border/50 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-3 pb-3 px-4 text-center">
                                    <svg class="w-6 h-6 mb-1 text-neutral-muted" id="upload_icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <p class="text-xs text-neutral-body" id="upload_text">
                                        @if(old('temp_image'))
                                            <span class="font-semibold text-emerald-700">Gambar sampul baru tersimpan</span> (klik untuk ganti)
                                        @else
                                            <span class="font-semibold text-primary">Pilih gambar sampul baru</span> untuk mengganti sampul
                                        @endif
                                    </p>
                                    <p class="text-[10px] text-neutral-muted tracking-wider uppercase mt-0.5" id="filename_display">
                                        {{ old('temp_image_name') ? 'Tersimpan: ' . old('temp_image_name') : 'Format: PNG, JPG, WEBP (Maksimal 2MB)' }}
                                    </p>
                                </div>
                                <input type="file" name="image" id="image_input" accept="image/png, image/jpeg, image/jpg, image/webp" class="hidden" onchange="previewFilename(this)" />
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('image')" class="mt-1.5" />
                    </div>
                </div>

                <div class="pt-6 border-t border-neutral-border flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.books.index') }}" class="btn-editorial-outline text-xs py-2 px-4 uppercase tracking-wider">Batal</a>
                    <x-button type="submit" variant="primary" class="text-xs py-2 px-6 uppercase tracking-wider font-semibold">Simpan Pembaruan</x-button>
                </div>
            </form>
        </x-card>
    </div>

    @push('scripts')
    <script>
        const originalCoverUrl = @json($book->image ? asset('storage/' . $book->image) : null);

        function formatRupiah(num) {
            return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
        }

        function syncFineScheme(triggeredBy = 'type') {
            const priceInput = document.getElementById('price');
            const fineTypeSelect = document.getElementById('fine_type');
            const fineValueInput = document.getElementById('fine_value');
            const hint = document.getElementById('fine_calculation_hint');

            if (!fineTypeSelect || !fineValueInput || !priceInput) return;

            const price = parseFloat(priceInput.value) || 0;
            const fineType = fineTypeSelect.value;
            let fineVal = (fineValueInput.value || '').trim();

            if (fineType === 'fixed') {
                // Skema Tetap: Otomatis mengikuti nilai taksiran harga buku fisik
                fineValueInput.value = price;
                fineValueInput.placeholder = 'Sesuai harga asli (' + price + ')';
                if (hint) {
                    hint.innerHTML = '<span class="text-primary font-semibold">Denda Tetap:</span> Otomatis 1x harga asli buku (' + formatRupiah(price) + ').';
                }
            } else if (fineType === 'multiplier') {
                // Skema Kelipatan: Menggunakan format kelipatan (misal 2x)
                if (triggeredBy === 'type') {
                    const num = parseFloat(fineVal.replace(/[^0-9.]/g, ''));
                    if (!fineVal || isNaN(num) || num > 20) {
                        fineValueInput.value = '2x';
                        fineVal = '2x';
                    } else if (!fineVal.toLowerCase().endsWith('x')) {
                        fineValueInput.value = num + 'x';
                        fineVal = num + 'x';
                    }
                }
                
                fineValueInput.placeholder = 'Contoh: 1x, 2x, 3x';
                const multNum = parseFloat(fineVal.replace(/[^0-9.]/g, '')) || 1;
                const totalFine = price * multNum;
                if (hint) {
                    hint.innerHTML = '<span class="text-primary font-semibold">Denda Kelipatan:</span> ' + multNum + 'x × ' + formatRupiah(price) + ' = <strong class="text-neutral-dark">' + formatRupiah(totalFine) + '</strong>.';
                }
            } else if (fineType === 'custom') {
                // Skema Manual: Nilai khusus bebas ditentukan pengelola
                fineValueInput.placeholder = 'Contoh: 75000';
                const customNominal = parseFloat(fineVal.replace(/[^0-9.]/g, '')) || 0;
                if (hint) {
                    hint.innerHTML = '<span class="text-primary font-semibold">Denda Manual:</span> Nominal ganti rugi ditetapkan khusus ' + formatRupiah(customNominal) + '.';
                }
            }
        }

        async function renderPdfCoverPreview(input) {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];
            const imageInput = document.getElementById('image_input');
            if (imageInput && imageInput.files && imageInput.files.length > 0) return;

            let attempts = 0;
            while (typeof window.renderPdfFirstPage !== 'function' && attempts < 30) {
                await new Promise(r => setTimeout(r, 100));
                attempts++;
            }

            if (typeof window.renderPdfFirstPage === 'function') {
                try {
                    const res = await window.renderPdfFirstPage(file, 10000);
                    if (res.success && res.imageBase64) {
                        const autoCoverInput = document.getElementById('auto_pdf_cover');
                        const previewImg = document.getElementById('cover_preview');
                        const wrapper = document.getElementById('cover_preview_wrapper');
                        const badge = document.getElementById('cover_badge');
                        const display = document.getElementById('filename_display');
                        if (autoCoverInput) autoCoverInput.value = res.imageBase64;
                        if (previewImg) previewImg.src = res.imageBase64;
                        if (wrapper) wrapper.classList.remove('hidden');
                        if (badge) {
                            badge.innerText = 'Sampul Otomatis (PDF Page 1)';
                            badge.className = 'absolute bottom-0 inset-x-0 bg-primary/90 text-[9px] text-white text-center py-0.5 font-semibold';
                        }
                        if (display && (!imageInput || !imageInput.files || imageInput.files.length === 0)) {
                            display.innerText = 'Sampul Otomatis Generasi PDF (Halaman 1)';
                            display.className = 'text-[10px] text-primary font-bold tracking-wider uppercase mt-0.5';
                        }
                    }
                } catch (e) {
                    console.warn('Manual edit PDF cover render failed:', e);
                }
            }
        }

        function resetCoverImage() {
            const input = document.getElementById('image_input');
            const previewWrapper = document.getElementById('cover_preview_wrapper');
            const previewImg = document.getElementById('cover_preview');
            const badge = document.getElementById('cover_badge');
            const btnReset = document.getElementById('btn_reset_cover');
            const display = document.getElementById('filename_display');
            const text = document.getElementById('upload_text');
            const icon = document.getElementById('upload_icon');

            if (input) input.value = '';

            if (originalCoverUrl) {
                if (previewImg) previewImg.src = originalCoverUrl;
                if (previewWrapper) previewWrapper.classList.remove('hidden');
                if (badge) {
                    badge.innerText = 'Sampul Aktif';
                    badge.className = 'absolute bottom-0 inset-x-0 bg-[#181818]/70 text-[9px] text-white text-center py-0.5 font-semibold';
                }
                if (btnReset) btnReset.classList.add('hidden');
            } else {
                if (previewImg) previewImg.src = '#';
                if (previewWrapper) previewWrapper.classList.add('hidden');
            }

            if (display) {
                display.innerText = 'Format: PNG, JPG, WEBP (Maksimal 2MB)';
                display.className = 'text-[10px] text-neutral-muted tracking-wider uppercase mt-0.5';
            }
            if (text) {
                text.innerHTML = '<span class="font-semibold text-primary">Pilih gambar sampul baru</span> untuk mengganti sampul';
            }
            if (icon) {
                icon.className = 'w-6 h-6 mb-1 text-neutral-muted';
            }
        }

        function previewFilename(input) {
            const display = document.getElementById('filename_display');
            const text = document.getElementById('upload_text');
            const icon = document.getElementById('upload_icon');
            const previewWrapper = document.getElementById('cover_preview_wrapper');
            const previewImg = document.getElementById('cover_preview');
            const badge = document.getElementById('cover_badge');
            const btnReset = document.getElementById('btn_reset_cover');
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const validTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];
                const validExtension = /\.(png|jpe?g|webp)$/i;

                // Validate that file is strictly an image
                if (!validTypes.includes(file.type) && !validExtension.test(file.name)) {
                    resetCoverImage();

                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Format Berkas Ditolak',
                            text: 'Sampul buku hanya boleh berupa file gambar (PNG, JPG, JPEG, WEBP). Berkas dokumen PDF tidak diperbolehkan.',
                            confirmButtonColor: '#C62828'
                        });
                    }
                    return;
                }

                // Validate maximum size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    resetCoverImage();

                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Ukuran Gambar Terlalu Besar',
                            text: 'Ukuran file gambar sampul melebihi batas maksimal 2MB. Silakan pilih gambar lain.',
                            confirmButtonColor: '#C62828'
                        });
                    }
                    return;
                }

                // Render live thumbnail preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (previewImg) previewImg.src = e.target.result;
                    if (previewWrapper) previewWrapper.classList.remove('hidden');
                    if (badge) {
                        badge.innerText = 'Sampul Baru';
                        badge.className = 'absolute bottom-0 inset-x-0 bg-primary/90 text-[9px] text-white text-center py-0.5 font-semibold';
                    }
                    if (btnReset) btnReset.classList.remove('hidden');
                };
                reader.readAsDataURL(file);

                const fileName = file.name;
                display.innerText = "Gambar terpilih: " + fileName + " (" + (file.size / 1024).toFixed(1) + " KB)";
                display.className = 'text-[10px] text-primary font-semibold tracking-wider mt-0.5';
                text.innerHTML = '<span class="font-semibold text-success">Gambar sampul valid & siap disimpan</span>';
                icon.className = 'w-6 h-6 mb-1 text-success';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Drop zone listeners
            const dropZone = document.getElementById('drop_zone');
            const imageInput = document.getElementById('image_input');

            if (dropZone && imageInput) {
                ['dragenter', 'dragover'].forEach(name => {
                    dropZone.addEventListener(name, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropZone.classList.add('border-primary', 'bg-primary-light/50');
                    });
                });

                ['dragleave', 'drop'].forEach(name => {
                    dropZone.addEventListener(name, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropZone.classList.remove('border-primary', 'bg-primary-light/50');
                    });
                });

                dropZone.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    if (dt && dt.files && dt.files.length) {
                        imageInput.files = dt.files;
                        previewFilename(imageInput);
                    }
                });
            }

            // Fine scheme auto sync listeners
            const priceInput = document.getElementById('price');
            const fineTypeSelect = document.getElementById('fine_type');
            const fineValueInput = document.getElementById('fine_value');

            if (priceInput) {
                priceInput.addEventListener('input', () => syncFineScheme('price'));
            }
            if (fineTypeSelect) {
                fineTypeSelect.addEventListener('change', () => syncFineScheme('type'));
            }
            if (fineValueInput) {
                fineValueInput.addEventListener('input', () => syncFineScheme('value'));
            }

            // Initial calculation
            syncFineScheme('init');
        });
    </script>
    @endpush
</x-app-layout>
