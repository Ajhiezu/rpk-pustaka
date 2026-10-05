<x-app-layout>
    <x-slot name="header">Import Massal Data Anggota</x-slot>

    <x-slot name="actions">
        <a href="{{ route('admin.users.index', ['role' => 'anggota']) }}"
            class="btn-editorial-outline text-xs py-2 px-4 shadow-xs">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Anggota
        </a>
    </x-slot>

    <div class="space-y-8 animate-in fade-in duration-300">

        {{-- Alert Messages --}}
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-lg text-sm flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- How it works --}}
        <div class="bg-white border border-neutral-border rounded-xl shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-border bg-neutral-surface flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-neutral-dark">Cara Kerja Import Massal Anggota</h2>
            </div>
            <div class="px-6 py-5">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    @foreach([
                        ['1', 'Unduh Template', 'Download file CSV template dengan format kolom yang benar'],
                        ['2', 'Isi Data Anggota', 'Isi nama, email, nomor HP, dan alamat untuk setiap anggota'],
                        ['3', 'Upload Spreadsheet', 'Upload file CSV/Excel (.xlsx, .xls, atau .csv)'],
                        ['4', 'Tinjau & Koreksi', 'Periksa data kandidat, perbaiki jika ada kesalahan'],
                        ['5', 'Konfirmasi Import', 'Klik "Import Semua" untuk membuat akun anggota sekaligus'],
                    ] as $step)
                        <div class="flex flex-col items-center text-center gap-2">
                            <div class="w-9 h-9 bg-primary text-white text-sm font-bold rounded-full flex items-center justify-center shadow-sm">
                                {{ $step[0] }}
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-neutral-dark">{{ $step[1] }}</p>
                                <p class="text-[11px] text-neutral-muted mt-0.5 leading-relaxed">{{ $step[2] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Format Kolom Info --}}
        <div class="bg-white border border-neutral-border rounded-xl shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-border bg-neutral-surface flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-accent-light flex items-center justify-center flex-shrink-0">
                        <svg class="w-4.5 h-4.5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-sm font-bold text-neutral-dark">Format Kolom Spreadsheet</h2>
                </div>
                <a href="{{ route('admin.members.import.template') }}"
                    class="btn-editorial-outline text-xs py-1.5 px-3.5 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Unduh Template CSV
                </a>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-neutral-surface">
                            <th class="px-4 py-2.5 text-left font-semibold text-neutral-dark uppercase tracking-wider border border-neutral-border">Kolom</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-neutral-dark uppercase tracking-wider border border-neutral-border">Nama Header Diterima</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-neutral-dark uppercase tracking-wider border border-neutral-border">Wajib</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-neutral-dark uppercase tracking-wider border border-neutral-border">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach([
                            ['Nama Lengkap', 'nama, name, nama lengkap', 'Ya', 'Nama lengkap anggota'],
                            ['Email', 'email, e-mail, alamat email', 'Ya', 'Email unik, digunakan untuk login'],
                            ['Password', 'password, kata_sandi, pass', 'Tidak', 'Password akun. Default jika kosong: password'],
                            ['Peran / Role', 'role, peran, hak akses, tipe', 'Tidak', 'Isi "anggota" atau "admin". Default: anggota'],
                            ['Nomor HP', 'no_telepon, phone, hp, no telepon', 'Tidak', 'Nomor telepon/WhatsApp anggota'],
                            ['Alamat', 'alamat, address, domisili', 'Tidak', 'Alamat domisili anggota'],
                        ] as $row)
                            <tr class="hover:bg-neutral-surface/50 transition-colors">
                                <td class="px-4 py-2.5 font-medium text-neutral-dark border border-neutral-border">{{ $row[0] }}</td>
                                <td class="px-4 py-2.5 text-neutral-muted font-mono border border-neutral-border">{{ $row[1] }}</td>
                                <td class="px-4 py-2.5 border border-neutral-border">
                                    @if($row[2] === 'Ya')
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-red-50 text-danger border border-red-200">Wajib</span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-neutral-surface text-neutral-muted border border-neutral-border">Opsional</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-neutral-body border border-neutral-border">{{ $row[3] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4 p-3.5 bg-blue-50 border border-blue-200 rounded-lg flex items-start gap-2.5 text-xs text-blue-700">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <strong>Catatan Penting:</strong> Password default untuk akun anggota hasil import adalah <code class="bg-blue-100 px-1.5 py-0.5 rounded font-mono font-bold">password</code> (atau disesuaikan jika terdapat kolom password di file CSV/Excel). Anggota dapat mengubah password setelah login.
                    </div>
                </div>
            </div>
        </div>

        {{-- Upload Form --}}
        <div class="bg-white border border-neutral-border rounded-xl shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-border bg-neutral-surface flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"></path>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-neutral-dark">Upload File Spreadsheet Anggota</h2>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.members.import.preview') }}" method="POST" enctype="multipart/form-data"
                    id="member-import-form">
                    @csrf

                    <div id="member-dropzone"
                        class="relative border-2 border-dashed border-neutral-border rounded-xl p-10 flex flex-col items-center justify-center gap-4 cursor-pointer hover:border-primary/50 hover:bg-primary-light/30 transition-all duration-200 group"
                        onclick="document.getElementById('member-file-input').click()">

                        <div class="w-16 h-16 rounded-full bg-primary-light flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-semibold text-neutral-dark">Seret & Lepas file di sini, atau <span class="text-primary underline">klik untuk memilih</span></p>
                            <p class="text-xs text-neutral-muted mt-1">Format didukung: <strong>.xlsx, .xls, .csv</strong> — Maksimum 10 MB</p>
                        </div>

                        {{-- File selected preview --}}
                        <div id="member-file-preview" class="hidden w-full max-w-sm">
                            <div class="flex items-center gap-3 p-3 bg-neutral-surface border border-neutral-border rounded-lg">
                                <div class="w-9 h-9 rounded bg-success/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p id="member-file-name" class="text-xs font-semibold text-neutral-dark truncate"></p>
                                    <p id="member-file-size" class="text-[11px] text-neutral-muted"></p>
                                </div>
                                <button type="button" onclick="event.stopPropagation(); clearMemberFile()"
                                    class="p-1 text-neutral-muted hover:text-danger hover:bg-red-50 rounded transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <input type="file" id="member-file-input" name="spreadsheet_file"
                            accept=".xlsx,.xls,.csv,.txt"
                            class="hidden" onchange="handleMemberFile(this)">
                    </div>

                    @error('spreadsheet_file')
                        <p class="mt-2 text-xs text-danger flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.users.index', ['role' => 'anggota']) }}" class="btn-editorial-outline text-xs py-2 px-5">Batal</a>
                        <button type="submit" id="member-submit-btn"
                            class="btn-editorial text-xs py-2 px-6 flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
                            disabled>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Proses & Pratinjau Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function handleMemberFile(input) {
            const file = input.files[0];
            if (!file) return;
            const preview = document.getElementById('member-file-preview');
            document.getElementById('member-file-name').textContent = file.name;
            document.getElementById('member-file-size').textContent = (file.size / 1024).toFixed(1) + ' KB';
            preview.classList.remove('hidden');
            document.getElementById('member-submit-btn').disabled = false;

            // Drag drop highlight
            const dropzone = document.getElementById('member-dropzone');
            dropzone.classList.add('border-primary', 'bg-primary-light/30');
        }

        function clearMemberFile() {
            document.getElementById('member-file-input').value = '';
            document.getElementById('member-file-preview').classList.add('hidden');
            document.getElementById('member-submit-btn').disabled = true;
            const dropzone = document.getElementById('member-dropzone');
            dropzone.classList.remove('border-primary', 'bg-primary-light/30');
        }

        // Drag and drop
        const dropzone = document.getElementById('member-dropzone');
        ['dragenter','dragover'].forEach(evt => {
            dropzone.addEventListener(evt, e => {
                e.preventDefault();
                dropzone.classList.add('border-primary', 'bg-primary-light/30', 'scale-[1.01]');
            });
        });
        ['dragleave','drop'].forEach(evt => {
            dropzone.addEventListener(evt, e => {
                e.preventDefault();
                dropzone.classList.remove('scale-[1.01]');
            });
        });
        dropzone.addEventListener('drop', e => {
            const file = e.dataTransfer.files[0];
            if (file) {
                const input = document.getElementById('member-file-input');
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                handleMemberFile(input);
            }
        });
    </script>
</x-app-layout>
