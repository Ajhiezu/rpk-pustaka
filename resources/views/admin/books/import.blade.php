<x-app-layout>
    <x-slot name="header">
        Import Massal Koleksi Buku, RPK PUSTAKA IMM SAINTEKMU
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('admin.books.index') }}" class="btn-editorial-outline text-xs py-2 px-4 shadow-xs">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Katalog
        </a>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6" x-data="bulkImportManager('{{ $batchId }}')">
        <!-- Editorial Navigation Tabs -->
        <div class="bg-white p-1.5 rounded-lg border border-neutral-border shadow-xs flex gap-2">
            <button type="button" @click="activeTab = 'digital'" 
                    :class="activeTab === 'digital' ? 'bg-primary text-white shadow-xs' : 'text-neutral-body hover:text-neutral-dark hover:bg-neutral-surface'"
                    class="flex-1 py-2.5 px-4 rounded-md text-xs font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Import Buku Digital (PDF / DOCX)
            </button>
            <button type="button" @click="activeTab = 'physical'" 
                    :class="activeTab === 'physical' ? 'bg-primary text-white shadow-xs' : 'text-neutral-body hover:text-neutral-dark hover:bg-neutral-surface'"
                    class="flex-1 py-2.5 px-4 rounded-md text-xs font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Import Buku Fisik (Excel / CSV)
            </button>
        </div>

        <!-- TAB 1: DIGITAL BOOKS IMPORT -->
        <div x-show="activeTab === 'digital'" class="space-y-6 animate-in fade-in duration-200">
            <!-- Instructions Banner -->
            <div class="bg-neutral-surface border border-neutral-border rounded-lg p-5">
                <div class="flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-full bg-primary-light text-primary flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="space-y-1 text-xs">
                        <h4 class="font-bold text-neutral-dark text-sm">Alur Import Buku Digital Massal</h4>
                        <p class="text-neutral-body leading-relaxed">
                            Pilih atau tarik hingga <strong>50 dokumen (PDF atau DOCX)</strong> sekaligus. Sistem akan mengekstrak judul dari nama berkas/metadata, menghitung jumlah halaman, serta <strong>merender sampul otomatis dari halaman pertama</strong>. Semua data akan disajikan di halaman Pratinjau untuk Anda periksa sebelum disimpan ke katalog.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Upload Dropzone -->
            <div class="bg-white border-2 border-dashed rounded-xl p-8 text-center transition-all cursor-pointer"
                 :class="isDragging ? 'border-primary bg-primary-light/40 ring-4 ring-primary/10' : 'border-neutral-border hover:border-primary/50 hover:bg-neutral-surface/40'"
                 @dragover.prevent="isDragging = true"
                 @dragleave.prevent="isDragging = false"
                 @drop.prevent="handleFileDrop($event)"
                 @click="$refs.digitalFileInput.click()">
                
                <input type="file" x-ref="digitalFileInput" multiple accept=".pdf,.docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="hidden" @change="handleFileSelect($event)">
                
                <div class="space-y-3 max-w-md mx-auto pointer-events-none">
                    <div class="w-14 h-14 rounded-full bg-primary-light text-primary mx-auto flex items-center justify-center shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    </div>
                    <div>
                        <p class="font-sans font-bold text-sm text-neutral-dark">Tarik & Lepaskan berkas PDF/DOCX di sini</p>
                        <p class="text-xs text-neutral-muted mt-1">atau klik untuk memilih dari perangkat (Maks. 50MB per berkas, hingga 50 naskah per sesi)</p>
                    </div>
                    <div class="flex items-center justify-center gap-2 pt-2">
                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 bg-neutral-surface border border-neutral-border rounded text-neutral-body">PDF Document</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 bg-neutral-surface border border-neutral-border rounded text-neutral-body">DOCX Document</span>
                    </div>
                </div>
            </div>

            <!-- Queue Progress Section (Shows when files are queued or uploading) -->
            <div x-show="fileQueue.length > 0" class="bg-white border border-neutral-border rounded-xl p-5 space-y-4 shadow-xs">
                <div class="flex items-center justify-between border-b border-neutral-border pb-3">
                    <div>
                        <h3 class="font-bold text-sm text-neutral-dark">Antrean Berkas Digital</h3>
                        <p class="text-xs text-neutral-muted" x-text="`${fileQueue.length} berkas dipilih • ${completedCount} berhasil diproses`"></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="resetQueue()" :disabled="isUploading" class="btn-editorial-outline text-xs py-1.5 px-3">
                            Bersihkan
                        </button>
                        <button type="button" @click="startUploadProcess()" :disabled="isUploading || fileQueue.length === 0" class="btn-editorial text-xs py-1.5 px-4">
                            <span x-show="!isUploading">Mulai Ekstraksi & Pratinjau</span>
                            <span x-show="isUploading" class="flex items-center gap-1.5">
                                <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Global Progress Bar -->
                <div x-show="isUploading || completedCount > 0" class="space-y-1.5">
                    <div class="flex justify-between text-xs font-semibold text-neutral-body">
                        <span x-text="currentStepMessage"></span>
                        <span x-text="`${progressPercent}%`"></span>
                    </div>
                    <div class="w-full h-2 bg-neutral-surface rounded-full overflow-hidden border border-neutral-border">
                        <div class="h-full bg-primary transition-all duration-300" :style="`width: ${progressPercent}%`"></div>
                    </div>
                </div>

                <!-- Individual File Queue Items -->
                <div class="max-h-72 overflow-y-auto divide-y divide-neutral-border/60">
                    <template x-for="(item, idx) in fileQueue" :key="idx">
                        <div class="py-2.5 flex items-center justify-between text-xs gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded bg-neutral-surface border border-neutral-border flex items-center justify-center shrink-0 font-mono font-bold text-[10px]"
                                     :class="item.name.toLowerCase().endsWith('.pdf') ? 'text-primary' : 'text-blue-600'"
                                     x-text="item.name.toLowerCase().endsWith('.pdf') ? 'PDF' : 'DOC'">
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-neutral-dark truncate max-w-sm" x-text="item.name"></p>
                                    <p class="text-[11px] text-neutral-muted" x-text="formatFileSize(item.size)"></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-[11px] px-2 py-0.5 rounded font-semibold"
                                      :class="{
                                          'bg-neutral-surface text-neutral-body': item.status === 'ready',
                                          'bg-[#FFF9ED] text-[#B45309]': item.status === 'rendering' || item.status === 'uploading',
                                          'bg-[#EDF7ED] text-success': item.status === 'done',
                                          'bg-[#FDEDED] text-danger': item.status === 'error'
                                      }"
                                      x-text="item.statusText">
                                </span>
                                <button type="button" @click="removeFromQueue(idx)" :disabled="isUploading" class="text-neutral-muted hover:text-danger p-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- TAB 2: PHYSICAL SPREADSHEET IMPORT -->
        <div x-show="activeTab === 'physical'" class="space-y-6 animate-in fade-in duration-200">
            <div class="bg-neutral-surface border border-neutral-border rounded-lg p-5">
                <div class="flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-full bg-primary-light text-primary flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div class="space-y-1 text-xs">
                        <h4 class="font-bold text-neutral-dark text-sm">Import Buku Fisik via Spreadsheet Excel / CSV</h4>
                        <p class="text-neutral-body leading-relaxed">
                            Unggah berkas <strong>.xlsx, .xls, atau .csv</strong> yang memuat daftar buku fisik perpustakaan. Sistem akan memetakan kolom secara otomatis (Judul, Penulis, Kategori, Stok, Lokasi Rak, Penerbit, Tahun, ISBN, dll) dan menyajikannya ke halaman Pratinjau.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Spreadsheet Format Reference Card -->
            <div class="bg-white border border-neutral-border rounded-xl p-5 space-y-3">
                <h4 class="text-xs font-bold uppercase tracking-wider text-neutral-muted">Format Kolom Spreadsheet yang Didukung:</h4>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                    <div class="p-2.5 bg-neutral-surface rounded border border-neutral-border">
                        <p class="font-bold text-neutral-dark">Judul <span class="text-danger">*</span></p>
                        <p class="text-[10px] text-neutral-muted">Wajib diisi</p>
                    </div>
                    <div class="p-2.5 bg-neutral-surface rounded border border-neutral-border">
                        <p class="font-bold text-neutral-dark">Penulis <span class="text-danger">*</span></p>
                        <p class="text-[10px] text-neutral-muted">Nama penulis/pengarang</p>
                    </div>
                    <div class="p-2.5 bg-neutral-surface rounded border border-neutral-border">
                        <p class="font-bold text-neutral-dark">Kategori <span class="text-danger">*</span></p>
                        <p class="text-[10px] text-neutral-muted">Sesuai kategori terdaftar</p>
                    </div>
                    <div class="p-2.5 bg-neutral-surface rounded border border-neutral-border">
                        <p class="font-bold text-neutral-dark">Lokasi / Rak <span class="text-danger">*</span></p>
                        <p class="text-[10px] text-neutral-muted">Nama lokasi rak fisik</p>
                    </div>
                    <div class="p-2.5 bg-neutral-surface rounded border border-neutral-border">
                        <p class="font-bold text-neutral-dark">Stok</p>
                        <p class="text-[10px] text-neutral-muted">Jumlah eksemplar (min. 1)</p>
                    </div>
                    <div class="p-2.5 bg-neutral-surface rounded border border-neutral-border">
                        <p class="font-bold text-neutral-dark">Penerbit</p>
                        <p class="text-[10px] text-neutral-muted">Nama penerbit buku</p>
                    </div>
                    <div class="p-2.5 bg-neutral-surface rounded border border-neutral-border">
                        <p class="font-bold text-neutral-dark">Tahun</p>
                        <p class="text-[10px] text-neutral-muted">Contoh: 2024</p>
                    </div>
                    <div class="p-2.5 bg-neutral-surface rounded border border-neutral-border">
                        <p class="font-bold text-neutral-dark">ISBN</p>
                        <p class="text-[10px] text-neutral-muted">Nomor ISBN 10/13</p>
                    </div>
                </div>
            </div>

            <!-- Upload Form -->
            <form action="{{ route('admin.books.import.spreadsheet') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-neutral-border rounded-xl p-6 space-y-4 shadow-xs">
                @csrf
                <input type="hidden" name="batch_id" value="{{ $batchId }}">
                
                <div class="space-y-2">
                    <label class="block font-sans text-xs font-bold uppercase tracking-wider text-neutral-dark">
                        Pilih Berkas Spreadsheet (.xlsx, .xls, .csv)
                    </label>
                    <input type="file" name="spreadsheet_file" accept=".xlsx,.xls,.csv" required
                           class="w-full text-xs text-neutral-dark file:mr-4 file:py-2.5 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary-light file:text-primary hover:file:bg-red-100 cursor-pointer border border-neutral-border rounded-md p-1.5">
                </div>

                <div class="pt-2 flex justify-end">
                    <x-button type="submit" variant="primary" class="px-6 py-2.5 text-xs uppercase tracking-wider font-semibold">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Baca Spreadsheet & Buka Pratinjau
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function bulkImportManager(batchId) {
            return {
                batchId: batchId,
                activeTab: 'digital',
                isDragging: false,
                isUploading: false,
                fileQueue: [],
                completedCount: 0,
                currentStepMessage: 'Siap memproses berkas',
                progressPercent: 0,

                handleFileDrop(e) {
                    this.isDragging = false;
                    const files = Array.from(e.dataTransfer.files).filter(f => {
                        const name = f.name.toLowerCase();
                        return name.endsWith('.pdf') || name.endsWith('.docx');
                    });
                    this.addFilesToQueue(files);
                },

                handleFileSelect(e) {
                    const files = Array.from(e.target.files);
                    this.addFilesToQueue(files);
                    e.target.value = '';
                },

                addFilesToQueue(files) {
                    if (this.fileQueue.length + files.length > 50) {
                        alert('Maksimal 50 berkas dalam satu sesi import massal.');
                        files = files.slice(0, 50 - this.fileQueue.length);
                    }

                    files.forEach(file => {
                        this.fileQueue.push({
                            file: file,
                            name: file.name,
                            size: file.size,
                            status: 'ready',
                            statusText: 'Siap',
                            renderedCover: null,
                        });
                    });
                },

                removeFromQueue(idx) {
                    if (!this.isUploading) {
                        this.fileQueue.splice(idx, 1);
                    }
                },

                resetQueue() {
                    if (!this.isUploading) {
                        this.fileQueue = [];
                        this.completedCount = 0;
                        this.progressPercent = 0;
                        this.currentStepMessage = 'Siap memproses berkas';
                    }
                },

                formatFileSize(bytes) {
                    if (bytes === 0) return '0 B';
                    const k = 1024;
                    const sizes = ['B', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
                },

                async startUploadProcess() {
                    if (this.fileQueue.length === 0 || this.isUploading) return;
                    this.isUploading = true;
                    this.completedCount = 0;

                    const totalFiles = this.fileQueue.length;
                    this.progressPercent = 5;

                    // Step 1: Render PDF covers with PDF.js (scale 1.5, canvas -> WebP blob)
                    for (let idx = 0; idx < totalFiles; idx++) {
                        const item = this.fileQueue[idx];
                        if (item.name.toLowerCase().endsWith('.pdf')) {
                            item.status = 'rendering';
                            item.statusText = `Merender sampul (${idx + 1}/${totalFiles})...`;
                            this.currentStepMessage = `Merender sampul hal 1: ${item.name}...`;
                            try {
                                if (window.renderPdfFirstPage) {
                                    const renderRes = await window.renderPdfFirstPage(item.file, 12000);
                                    if (renderRes.success && (renderRes.blob || renderRes.imageBase64)) {
                                        item.coverBlob = renderRes.blob;
                                        item.renderedCover = renderRes.imageBase64;
                                        item.statusText = 'Sampul hal 1 OK';
                                    } else {
                                        item.statusText = 'Sampul manual';
                                    }
                                }
                            } catch (err) {
                                console.warn('Render skipped:', err);
                                item.statusText = 'Sampul manual';
                            }
                        } else {
                            item.statusText = 'DOCX siap';
                        }
                        this.progressPercent = 5 + Math.round(((idx + 1) / totalFiles) * 30);
                    }

                    this.progressPercent = 35;

                    // Step 2: Upload 1-by-1 sequentially to prevent post_max_size / timeout issues on bulk uploads
                    const batchChunkSize = 1;
                    for (let i = 0; i < totalFiles; i += batchChunkSize) {
                        const chunk = this.fileQueue.slice(i, i + batchChunkSize);
                        const formData = new FormData();
                        formData.append('batch_id', this.batchId);
                        formData.append('_token', '{{ csrf_token() }}');

                        chunk.forEach((item, chunkIdx) => {
                            item.status = 'uploading';
                            item.statusText = 'Mengunggah...';
                            formData.append(`files[${chunkIdx}]`, item.file);
                            if (item.coverBlob) {
                                formData.append(`rendered_covers[${chunkIdx}]`, item.coverBlob, `cover_${chunkIdx}.webp`);
                            } else if (item.renderedCover) {
                                formData.append(`rendered_covers[${chunkIdx}]`, item.renderedCover);
                            }
                        });

                        this.currentStepMessage = `Mengunggah & memproses naskah (${Math.min(i + batchChunkSize, totalFiles)}/${totalFiles})...`;

                        try {
                            const response = await fetch('{{ route("admin.books.import.digital") }}', {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                },
                                body: formData,
                            });

                            if (!response.ok) {
                                let errLabel = `Gagal (HTTP ${response.status})`;
                                if (response.status === 413) errLabel = 'File Terlalu Besar';
                                if (response.status === 500) errLabel = 'Gagal Server';
                                chunk.forEach(item => {
                                    item.status = 'error';
                                    item.statusText = errLabel;
                                });
                            } else {
                                const data = await response.json();
                                if (data.success) {
                                    chunk.forEach(item => {
                                        item.status = 'done';
                                        item.statusText = '✓ Selesai';
                                        this.completedCount++;
                                    });
                                } else {
                                    chunk.forEach(item => {
                                        item.status = 'error';
                                        item.statusText = 'Gagal Ekstraksi';
                                    });
                                }
                            }
                        } catch (uploadErr) {
                            console.error('File upload error:', uploadErr);
                            chunk.forEach(item => {
                                item.status = 'error';
                                item.statusText = 'Gagal Koneksi';
                            });
                        }

                        this.progressPercent = 35 + Math.round(((i + chunk.length) / totalFiles) * 65);
                    }

                    this.progressPercent = 100;
                    this.isUploading = false;

                    if (this.completedCount > 0) {
                        this.currentStepMessage = `Selesai! ${this.completedCount} berkas berhasil diproses. Mengarahkan ke Pratinjau...`;
                        setTimeout(() => {
                            window.location.href = `{{ route('admin.books.import.preview') }}?batch_id=${this.batchId}`;
                        }, 800);
                    } else {
                        this.currentStepMessage = 'Tidak ada berkas yang berhasil diproses. Silakan periksa pesan kesalahan.';
                    }
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
