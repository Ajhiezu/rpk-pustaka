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

    <div class="max-w-5xl mx-auto space-y-6" x-data="bulkImportManager('{{ $batchId }}', {{ json_encode($existingCandidates ?? []) }})">
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
                            Pilih atau tarik berkas dokumen digital <strong>(PDF atau DOCX, hingga 50MB per berkas)</strong>. Sistem akan mengekstrak judul dari nama berkas/metadata, menghitung jumlah halaman, serta <strong>merender sampul otomatis dari halaman pertama</strong>. Semua data akan disajikan di halaman Pratinjau untuk Anda periksa sebelum disimpan ke katalog.
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
                        <p class="text-xs text-neutral-muted mt-1">atau klik untuk memilih dari perangkat (Hingga 50MB per berkas, hingga 50 naskah per sesi)</p>
                    </div>
                    <div class="flex items-center justify-center gap-2 pt-2">
                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 bg-neutral-surface border border-neutral-border rounded text-neutral-body">PDF Document (Maks 50MB)</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 bg-neutral-surface border border-neutral-border rounded text-neutral-body">DOCX Document</span>
                    </div>
                </div>
            </div>

            <!-- Rejected Files (>50MB Takedown) Alert Banner -->
            <div x-show="rejectedFilesList.length > 0" class="bg-[#FDEDED] border border-[#FFCDD2] rounded-xl p-4 space-y-2.5 shadow-2xs">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-danger font-bold text-xs uppercase tracking-wider">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Berkas Dikeluarkan / Ditakedown (Melebihi Batas 50MB)</span>
                    </div>
                    <button type="button" @click="rejectedFilesList = []" class="text-[11px] font-semibold text-danger hover:underline cursor-pointer">
                        Tutup Info
                    </button>
                </div>
                <p class="text-[11px] text-neutral-dark">
                    Sistem otomatis mengeluarkan dan men-takedown berkas berikut karena ukurannya melebihi batas maksimal <strong>50MB per naskah digital</strong>:
                </p>
                <div class="divide-y divide-red-200/80 text-xs bg-white/70 rounded-lg p-2.5 border border-red-200 max-h-36 overflow-y-auto">
                    <template x-for="(rf, rIdx) in rejectedFilesList" :key="rIdx">
                        <div class="py-1.5 flex items-center justify-between gap-3 text-xs">
                            <span class="font-semibold text-neutral-dark truncate" x-text="rf.name"></span>
                            <span class="text-danger font-mono font-bold shrink-0 text-[11px]" x-text="`${rf.sizeFormatted} • Ditakedown`"></span>
                        </div>
                    </template>
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
                        <button type="button" @click="resetQueue()" class="btn-editorial-outline text-xs py-1.5 px-3 cursor-pointer">
                            Bersihkan
                        </button>
                        <button type="button" @click="$refs.digitalFileInput.click()" class="btn-editorial-outline text-xs py-1.5 px-3 cursor-pointer">
                            + Tambah Berkas
                        </button>
                        <button type="button" x-show="isUploading" @click="cancelUploadProcess()" class="bg-red-50 hover:bg-red-100 text-danger border border-red-200 font-bold text-xs py-1.5 px-3 rounded transition-all cursor-pointer flex items-center gap-1.5 shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Batal
                        </button>
                        <button type="button" x-show="!isUploading && fileQueue.length > 0 && fileQueue.some(i => i.status !== 'done')" @click="resumeUploadProcess()" class="bg-[#FEF2F2] hover:bg-red-100 text-primary border border-primary/30 font-bold text-xs py-1.5 px-3.5 rounded transition-all cursor-pointer flex items-center gap-1.5 shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Lanjutkan Proses
                        </button>
                        <button type="button" 
                                @click="goToPreview()" 
                                :disabled="completedCount === 0 || isUploading" 
                                :class="isUploading ? 'opacity-50 cursor-not-allowed' : ''"
                                class="btn-editorial text-xs py-1.5 px-4 cursor-pointer flex items-center gap-1.5 shadow-xs transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                                :title="isUploading ? 'Proses unggah sedang berjalan. Harap tunggu hingga selesai atau klik Batal terlebih dahulu.' : ''">
                            <span x-show="isUploading" class="animate-spin w-3 h-3 border-2 border-white border-t-transparent rounded-full"></span>
                            <span x-text="isUploading ? `Mengunggah (${completedCount}/${fileQueue.length})...` : (completedCount === fileQueue.length && completedCount > 0 ? 'Buka Pratinjau →' : (completedCount > 0 ? `Lanjut ke Pratinjau (${completedCount}/${fileQueue.length}) →` : 'Menunggu Berkas...'))"></span>
                        </button>
                    </div>
                </div>

                <!-- Global Progress Bar -->
                <div x-show="isUploading || completedCount > 0 || fileQueue.some(i => i.status === 'cancelled')" class="space-y-1.5">
                    <div class="flex justify-between text-xs font-semibold text-neutral-body">
                        <span x-text="currentStepMessage"></span>
                        <span x-text="`${progressPercent}%`"></span>
                    </div>
                    <div class="w-full h-2 bg-neutral-surface rounded-full overflow-hidden border border-neutral-border">
                        <div class="h-full bg-primary transition-all duration-300" :style="`width: ${progressPercent}%`"></div>
                    </div>
                </div>

                <!-- Individual File Queue Items -->
                <div class="max-h-80 overflow-y-auto divide-y divide-neutral-border/60">
                    <template x-for="(item, idx) in fileQueue" :key="idx">
                        <div class="py-2.5 flex items-center justify-between text-xs gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <!-- Real Cover Thumbnail (Page 1) or Document Badge -->
                                <div class="w-9 h-12 rounded bg-neutral-surface border border-neutral-border flex items-center justify-center shrink-0 overflow-hidden shadow-2xs">
                                    <template x-if="item.coverUrl">
                                        <img :src="item.coverUrl" alt="Sampul" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!item.coverUrl">
                                        <span class="font-mono font-bold text-[10px]"
                                              :class="item.name.toLowerCase().endsWith('.pdf') ? 'text-primary' : 'text-blue-600'"
                                              x-text="item.name.toLowerCase().endsWith('.pdf') ? 'PDF' : 'DOC'">
                                        </span>
                                    </template>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-neutral-dark truncate max-w-sm" x-text="item.name"></p>
                                    <div class="flex items-center gap-2 text-[11px] text-neutral-muted">
                                        <span x-text="formatFileSize(item.size)"></span>
                                        <template x-if="item.pageCount">
                                            <span>• <span x-text="`${item.pageCount} Hal.`"></span></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <div x-show="item.status === 'uploading' && item.uploadPercent > 0" class="w-16 sm:w-24 h-1.5 bg-neutral-surface rounded-full overflow-hidden border border-neutral-border">
                                    <div class="h-full bg-primary transition-all duration-150 rounded-full" :style="`width: ${item.uploadPercent}%`"></div>
                                </div>
                                <button type="button" @click="retrySingleItem(idx)" x-show="item.status === 'error' || item.status === 'cancelled'" :disabled="isUploading"
                                        class="text-[11px] px-2 py-0.5 rounded font-bold bg-primary-light text-primary hover:bg-red-100 transition-colors cursor-pointer">
                                    <span x-text="item.status === 'error' ? 'Coba Lagi' : 'Lanjutkan'"></span>
                                </button>
                                <span class="text-[11px] px-2.5 py-0.5 rounded font-semibold whitespace-nowrap"
                                      :class="{
                                          'bg-neutral-surface text-neutral-body': item.status === 'ready',
                                          'bg-[#FFF9ED] text-[#B45309]': item.status === 'rendering' || item.status === 'uploading',
                                          'bg-[#EDF7ED] text-success': item.status === 'done',
                                          'bg-[#FDEDED] text-danger': item.status === 'error',
                                          'bg-amber-50 text-amber-700': item.status === 'cancelled'
                                      }"
                                      x-text="item.statusText">
                                </span>
                                <!-- Takedown / Hapus Item Button -->
                                <button type="button" @click="takedownItem(idx)" class="text-neutral-muted hover:text-danger hover:bg-red-50 p-1.5 rounded transition-all cursor-pointer flex items-center gap-1" title="Takedown / Buang naskah ini dari antrean">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span class="text-[11px] font-medium hidden sm:inline">Buang</span>
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

            <!-- Download Official Template Banner -->
            <div class="bg-white border border-neutral-border rounded-xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xs">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-lg bg-[#EDF7ED] text-success border border-[#C8E6C9] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-neutral-dark text-xs">Unduh Template Berkas Spreadsheet Excel (.xlsx)</h4>
                        <p class="text-[11px] text-neutral-body mt-0.5">Gunakan berkas template resmi ini dengan kolom rapi terpisah (Kolom A Judul, Kolom B Penulis, dst) untuk import data buku fisik secara instan.</p>
                    </div>
                </div>
                <a href="{{ route('admin.books.import.template') }}" class="btn-editorial-outline text-xs py-2.5 px-4 shadow-xs shrink-0 flex items-center gap-2 font-bold uppercase tracking-wider text-primary border-primary hover:bg-primary-light">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Unduh Template Excel (.xlsx)
                </a>
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
        const DB_NAME = 'RpkPustakaImportDB';
        const DB_STORE = 'batches';

        function openImportDB() {
            return new Promise((resolve) => {
                try {
                    const req = indexedDB.open(DB_NAME, 1);
                    req.onupgradeneeded = (e) => {
                        const db = e.target.result;
                        if (!db.objectStoreNames.contains(DB_STORE)) {
                            db.createObjectStore(DB_STORE, { keyPath: 'batchId' });
                        }
                    };
                    req.onsuccess = () => resolve(req.result);
                    req.onerror = () => resolve(null);
                } catch (e) {
                    resolve(null);
                }
            });
        }

        async function persistBatchQueue(batchId, queue) {
            try {
                const db = await openImportDB();
                if (!db) return;
                const tx = db.transaction(DB_STORE, 'readwrite');
                const store = tx.objectStore(DB_STORE);
                const items = queue.map(i => ({
                    file: i.file,
                    name: i.name,
                    size: i.size,
                    status: i.status,
                    statusText: i.statusText,
                    uploadPercent: i.uploadPercent,
                    coverUrl: i.coverUrl,
                    pageCount: i.pageCount,
                    candidateId: i.candidateId
                }));
                store.put({ batchId, items, updatedAt: Date.now() });
            } catch (e) {
                console.warn('Gagal menyimpan ke IndexedDB:', e);
            }
        }

        async function loadPersistedQueue(batchId) {
            try {
                const db = await openImportDB();
                if (!db) return null;
                return new Promise((resolve) => {
                    const tx = db.transaction(DB_STORE, 'readonly');
                    const store = tx.objectStore(DB_STORE);
                    const req = store.get(batchId);
                    req.onsuccess = () => resolve(req.result ? req.result.items : null);
                    req.onerror = () => resolve(null);
                });
            } catch (e) {
                return null;
            }
        }

        async function clearPersistedQueue(batchId) {
            try {
                const db = await openImportDB();
                if (!db) return;
                const tx = db.transaction(DB_STORE, 'readwrite');
                tx.objectStore(DB_STORE).delete(batchId);
            } catch (e) {}
        }

        function bulkImportManager(batchId, existingCandidates = []) {
            return {
                batchId: batchId,
                activeTab: 'digital',
                isDragging: false,
                isUploading: false,
                isCancelled: false,
                activeXhrs: [],
                fileQueue: [],
                rejectedFilesList: [],
                completedCount: 0,
                currentStepMessage: 'Siap memproses berkas',
                progressPercent: 0,

                async init() {
                    const existingMap = new Map();
                    if (Array.isArray(existingCandidates) && existingCandidates.length > 0) {
                        existingCandidates.forEach(cand => {
                            existingMap.set(cand.id, cand);
                            this.fileQueue.push({
                                file: null,
                                name: cand.original_filename || 'Dokumen Digital',
                                size: 0,
                                status: 'done',
                                statusText: '✓ Siap (Tersimpan)',
                                uploadPercent: 100,
                                coverUrl: cand.cover_path ? ('/storage/' + cand.cover_path) : null,
                                pageCount: cand.page_count,
                                candidateId: cand.id,
                                skipPdfParse: true,
                            });
                        });
                        this.completedCount = existingCandidates.length;
                    }

                    const savedItems = await loadPersistedQueue(this.batchId);
                    if (Array.isArray(savedItems) && savedItems.length > 0) {
                        let hasUnfinished = false;
                        savedItems.forEach(item => {
                            const alreadyAdded = this.fileQueue.some(q => 
                                (item.candidateId && q.candidateId === item.candidateId) || 
                                (q.name === item.name && q.status === 'done')
                            );
                            if (!alreadyAdded) {
                                const isDone = item.status === 'done' || (item.candidateId && existingMap.has(item.candidateId));
                                this.fileQueue.push({
                                    file: item.file,
                                    name: item.name,
                                    size: item.size,
                                    status: isDone ? 'done' : 'ready',
                                    statusText: isDone ? '✓ Siap' : 'Menunggu...',
                                    uploadPercent: isDone ? 100 : 0,
                                    coverUrl: item.coverUrl,
                                    pageCount: item.pageCount,
                                    candidateId: item.candidateId,
                                    skipPdfParse: true,
                                });
                                if (isDone) {
                                    this.completedCount++;
                                } else {
                                    hasUnfinished = true;
                                }
                            }
                        });

                        this.updateGlobalProgress();

                        if (hasUnfinished) {
                            this.currentStepMessage = 'Memulihkan antrean dan melanjutkan berkas yang belum selesai...';
                            this.$nextTick(() => {
                                this.startUploadProcess();
                            });
                        }
                    }

                    window.addEventListener('pageshow', () => {
                        const hasUnfinished = this.fileQueue.some(i => i.status === 'ready' || i.status === 'cancelled');
                        if (hasUnfinished && !this.isUploading) {
                            this.resumeUploadProcess();
                        }
                    });
                },

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
                    const MAX_FILE_SIZE = 50 * 1024 * 1024; // 50MB limit
                    const validFiles = [];

                    files.forEach(file => {
                        if (file.size > MAX_FILE_SIZE) {
                            this.rejectedFilesList.push({
                                name: file.name,
                                size: file.size,
                                sizeFormatted: this.formatFileSize(file.size),
                                reason: 'Ukuran melebihi batas 50MB'
                            });
                        } else {
                            validFiles.push(file);
                        }
                    });

                    if (this.fileQueue.length + validFiles.length > 50) {
                        alert('Maksimal 50 berkas dalam satu sesi import massal.');
                        validFiles.splice(50 - this.fileQueue.length);
                    }

                    validFiles.forEach(file => {
                        this.fileQueue.push({
                            file: file,
                            name: file.name,
                            size: file.size,
                            status: 'ready',
                            statusText: 'Antrean...',
                            uploadPercent: 0,
                            coverUrl: null,
                            pageCount: null,
                            candidateId: null,
                            skipPdfParse: true,
                        });
                    });

                    persistBatchQueue(this.batchId, this.fileQueue);

                    this.$nextTick(() => {
                        this.startUploadProcess();
                    });
                },

                async takedownItem(idx) {
                    const item = this.fileQueue[idx];
                    if (!item) return;

                    if (item._xhr) {
                        try { item._xhr.abort(); } catch (e) {}
                    }

                    if (item.candidateId) {
                        try {
                            fetch('{{ route("admin.books.import.remove") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    batch_id: this.batchId,
                                    candidate_id: item.candidateId
                                })
                            }).catch(() => {});
                        } catch (e) {}
                    }

                    if (item.status === 'done') {
                        this.completedCount = Math.max(0, this.completedCount - 1);
                    }

                    this.fileQueue.splice(idx, 1);
                    this.updateGlobalProgress();
                    persistBatchQueue(this.batchId, this.fileQueue);

                    if (this.fileQueue.length === 0) {
                        this.currentStepMessage = 'Antrean kosong.';
                        this.isUploading = false;
                        clearPersistedQueue(this.batchId);
                    }
                },

                cancelUploadProcess() {
                    this.isCancelled = true;
                    this.isUploading = false;

                    if (Array.isArray(this.activeXhrs)) {
                        this.activeXhrs.forEach(xhr => {
                            try { xhr.abort(); } catch (e) {}
                        });
                        this.activeXhrs = [];
                    }

                    this.fileQueue.forEach(item => {
                        if (item.status !== 'done') {
                            item.status = 'cancelled';
                            item.statusText = 'Dibatalkan';
                            item.uploadPercent = 0;
                            delete item._xhr;
                        }
                    });

                    // Langsung sinkronkan progres ke persentase berkas yang sudah berhasil (terkunci stabil)
                    this.updateGlobalProgress();
                    persistBatchQueue(this.batchId, this.fileQueue);

                    const done = this.completedCount;
                    const total = this.fileQueue.length;
                    this.currentStepMessage = `Proses dihentikan (${done}/${total} selesai). Anda dapat klik "Lanjutkan Proses" kapan saja.`;
                },

                resumeUploadProcess() {
                    this.isCancelled = false;
                    this.fileQueue.forEach(item => {
                        if (item.status !== 'done') {
                            item.status = 'ready';
                            item.statusText = 'Menunggu...';
                            item.uploadPercent = 0;
                            delete item._xhr;
                        }
                    });
                    this.updateGlobalProgress();
                    this.currentStepMessage = 'Melanjutkan proses unggah antrean berkas...';
                    this.startUploadProcess();
                },

                resetQueue() {
                    if (this.isUploading) {
                        if (!confirm('Proses unggah sedang berlangsung. Yakin ingin membatalkan dan membersihkan seluruh antrean berkas?')) {
                            return;
                        }
                        this.cancelUploadProcess();
                    }
                    this.fileQueue = [];
                    this.completedCount = 0;
                    this.progressPercent = 0;
                    this.currentStepMessage = 'Siap memproses berkas';
                    clearPersistedQueue(this.batchId);
                },

                formatFileSize(bytes) {
                    if (!bytes || bytes === 0) return '0 B';
                    const k = 1024;
                    const sizes = ['B', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
                },

                updateGlobalProgress() {
                    const total = this.fileQueue.length;
                    if (total === 0) {
                        this.progressPercent = 0;
                        return;
                    }

                    const completed = this.fileQueue.filter(f => f.status === 'done').length;

                    // Basis persentase terkunci dari berkas yang sudah selesai 100%
                    let calculated = (completed / total) * 100;

                    // Tambahkan fraksi dari berkas yang sedang aktif mengunggah saat ini
                    this.fileQueue.forEach(item => {
                        if (item.status === 'uploading' && (item.uploadPercent || 0) > 0) {
                            calculated += ((item.uploadPercent || 0) / total);
                        }
                    });

                    this.progressPercent = Math.min(100, Math.max(0, Math.round(calculated)));
                },

                async goToPreview() {
                    if (this.isUploading) {
                        alert('Proses upload masih berjalan. Harap tunggu hingga seluruh berkas selesai atau klik "Batal" terlebih dahulu sebelum menuju pratinjau.');
                        return;
                    }
                    if (this.completedCount === 0) {
                        alert('Belum ada berkas yang selesai diunggah.');
                        return;
                    }
                    await persistBatchQueue(this.batchId, this.fileQueue);
                    window.location.href = `{{ route('admin.books.import.preview') }}?batch_id=${this.batchId}`;
                },

                async uploadSingleItem(item) {
                    if (this.isCancelled) return Promise.resolve(false);

                    let coverBlob = null;
                    let coverBase64 = null;
                    let pageCount = null;

                    if (item.file && item.file.name && item.file.name.toLowerCase().endsWith('.pdf') && typeof window.renderPdfFirstPage === 'function') {
                        item.status = 'rendering';
                        item.statusText = 'Membuat cover hal 1...';
                        try {
                            const renderRes = await window.renderPdfFirstPage(item.file);
                            if (renderRes && renderRes.success) {
                                item.coverUrl = renderRes.imageBase64;
                                item.pageCount = renderRes.pageCount;
                                coverBlob = renderRes.blob;
                                coverBase64 = renderRes.imageBase64;
                                pageCount = renderRes.pageCount;
                            }
                        } catch (err) {
                            console.warn('Cover rendering hal 1 dilewati:', err);
                        }
                    }

                    if (!item.file) {
                        return Promise.resolve(true);
                    }

                    return new Promise((resolve) => {
                        const formData = new FormData();
                        formData.append('batch_id', this.batchId);
                        formData.append('_token', '{{ csrf_token() }}');
                        formData.append('files[0]', item.file);
                        formData.append('skip_pdf_parse', '1');

                        if (coverBlob) {
                            formData.append('rendered_covers[0]', coverBlob, 'cover.webp');
                        } else if (coverBase64) {
                            formData.append('rendered_covers[0]', coverBase64);
                        }

                        if (pageCount) {
                            formData.append('page_count', pageCount);
                        }

                        item.status = 'uploading';
                        item.uploadPercent = 0;
                        item.statusText = '0% • Mengunggah...';

                        const xhr = new XMLHttpRequest();
                        item._xhr = xhr;
                        this.activeXhrs.push(xhr);

                        const cleanup = () => {
                            const pos = this.activeXhrs.indexOf(xhr);
                            if (pos !== -1) this.activeXhrs.splice(pos, 1);
                            delete item._xhr;
                        };

                        xhr.upload.onprogress = (e) => {
                            if (this.isCancelled) return;
                            if (e.lengthComputable) {
                                if (e.loaded < e.total) {
                                    const percent = Math.min(99, Math.round((e.loaded / e.total) * 100));
                                    item.uploadPercent = percent;
                                    const loadedStr = this.formatFileSize(e.loaded);
                                    const totalStr = this.formatFileSize(e.total);
                                    item.statusText = `${percent}% (${loadedStr} / ${totalStr})`;
                                } else {
                                    item.uploadPercent = 99;
                                    item.statusText = '99% • Menyimpan di server...';
                                }
                                this.updateGlobalProgress();
                            }
                        };

                        xhr.onload = () => {
                            cleanup();
                            if (this.isCancelled) {
                                resolve(false);
                                return;
                            }

                            if (xhr.status >= 200 && xhr.status < 300) {
                                try {
                                    const data = JSON.parse(xhr.responseText);
                                    if (data.success) {
                                        item.status = 'done';
                                        item.uploadPercent = 100;
                                        item.statusText = '✓ Siap';
                                        if (data.processed && data.processed[0] && data.processed[0].id) {
                                            item.candidateId = data.processed[0].id;
                                        }
                                        this.completedCount++;
                                        this.updateGlobalProgress();
                                        persistBatchQueue(this.batchId, this.fileQueue);
                                        resolve(true);
                                        return;
                                    } else {
                                        item.status = 'error';
                                        item.statusText = data.message || 'Gagal Ekstraksi';
                                        resolve(false);
                                        return;
                                    }
                                } catch (parseErr) {
                                    item.status = 'error';
                                    item.statusText = 'Respon Tidak Valid';
                                    resolve(false);
                                    return;
                                }
                            } else {
                                let errLabel = `Gagal (HTTP ${xhr.status})`;
                                if (xhr.status === 413) errLabel = 'File Terlalu Besar (>50MB)';
                                if (xhr.status === 500) errLabel = 'Gagal Server';
                                if (xhr.status === 503) errLabel = 'Server Sibuk (HTTP 503)';
                                if (xhr.status === 504) errLabel = 'Timeout Gateway (HTTP 504)';
                                item.status = 'error';
                                item.statusText = errLabel;
                                resolve(false);
                            }
                        };

                        xhr.onerror = () => {
                            cleanup();
                            if (this.isCancelled) {
                                resolve(false);
                                return;
                            }
                            item.status = 'error';
                            item.statusText = 'Koneksi Terputus';
                            resolve(false);
                        };

                        xhr.onabort = () => {
                            cleanup();
                            item.status = 'cancelled';
                            item.statusText = 'Dibatalkan';
                            item.uploadPercent = 0;
                            this.updateGlobalProgress();
                            resolve(false);
                        };

                        xhr.open('POST', '{{ route("admin.books.import.digital") }}');
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        xhr.setRequestHeader('Accept', 'application/json');
                        xhr.send(formData);
                    });
                },

                async retrySingleItem(idx) {
                    const item = this.fileQueue[idx];
                    if (!item) return;

                    this.isCancelled = false;
                    item.status = 'ready';
                    item.statusText = 'Menunggu...';
                    item.uploadPercent = 0;
                    delete item._xhr;
                    this.startUploadProcess();
                },

                async startUploadProcess() {
                    if (this.fileQueue.length === 0) return;
                    if (this.isUploading) return;

                    this.isUploading = true;
                    this.isCancelled = false;

                    // Optimasi 1 berkas per 1 berkas: 1 file selesai penuh, baru lanjut membaca berkas berikutnya
                    while (!this.isCancelled) {
                        const nextItem = this.fileQueue.find(i => i.status === 'ready');
                        if (!nextItem) break;

                        const done = this.completedCount;
                        const total = this.fileQueue.length;
                        this.currentStepMessage = `Memproses (${done + 1}/${total}): ${nextItem.name}...`;

                        await this.uploadSingleItem(nextItem);
                    }

                    this.isUploading = false;
                    this.updateGlobalProgress();

                    const total = this.fileQueue.length;
                    if (this.completedCount > 0 && !this.isCancelled) {
                        if (this.completedCount === total) {
                            this.currentStepMessage = `Selesai! Seluruh ${this.completedCount} berkas berhasil diunggah. Mengarahkan ke Pratinjau...`;
                            setTimeout(() => {
                                if (!this.isCancelled) {
                                    this.goToPreview();
                                }
                            }, 800);
                        } else {
                            this.currentStepMessage = `${this.completedCount} dari ${total} berkas selesai diunggah. Klik "Lanjut ke Pratinjau" untuk melanjutkan.`;
                        }
                    }
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
