<x-app-layout>
    <x-slot name="header">Pratinjau Kandidat Anggota</x-slot>

    <x-slot name="actions">
        <a href="{{ route('admin.members.import.create') }}"
            class="btn-editorial-outline text-xs py-2 px-4 shadow-xs">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Upload Ulang
        </a>
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300">

        {{-- Summary Stats --}}
        @php
            $totalCandidates = count($candidates);
            $okCount = collect($candidates)->where('status', 'OK')->count();
            $dupCount = collect($candidates)->where('status', 'DUPLICATE')->count();
            $errCount = collect($candidates)->where('status', 'ERROR')->count();
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([
                ['Total Kandidat', $totalCandidates, 'text-neutral-dark', 'bg-neutral-surface', 'border-neutral-border'],
                ['Siap Import', $okCount, 'text-success', 'bg-green-50', 'border-green-200'],
                ['Email Duplikat', $dupCount, 'text-accent', 'bg-accent-light', 'border-[#FDE68A]'],
                ['Perlu Koreksi', $errCount, 'text-danger', 'bg-red-50', 'border-red-200'],
            ] as [$label, $count, $textClass, $bgClass, $borderClass])
                <div class="p-4 {{ $bgClass }} border {{ $borderClass }} rounded-xl text-center">
                    <p class="text-2xl font-bold {{ $textClass }}">{{ $count }}</p>
                    <p class="text-[11px] font-semibold text-neutral-muted uppercase tracking-wider mt-1">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        @if($dupCount > 0)
            <div class="bg-accent-light border border-[#FDE68A] text-neutral-dark px-5 py-3.5 rounded-lg text-xs flex items-start gap-3">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
                </svg>
                <span><strong>{{ $dupCount }} email sudah terdaftar</strong> di sistem dan akan otomatis dilewati saat import. Baris ini ditandai oranye dan tidak dapat dicentang.</span>
            </div>
        @endif

        @if($errCount > 0)
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-lg text-xs flex items-start gap-3">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
                </svg>
                <span><strong>{{ $errCount }} baris memiliki error</strong> (nama/email kosong atau format salah). Perbaiki data di bawah sebelum klik Import.</span>
            </div>
        @endif

        {{-- Candidate Table --}}
        <form action="{{ route('admin.members.import.store') }}" method="POST" id="import-form">
            @csrf
            <input type="hidden" name="batch_id" value="{{ $batchId }}">

            <div class="bg-white border border-neutral-border rounded-xl shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-border bg-neutral-surface flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4.5 h-4.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-sm font-bold text-neutral-dark">{{ $totalCandidates }} Kandidat Akun Anggota</h2>
                    </div>
                    <label class="flex items-center gap-2 text-xs text-neutral-body cursor-pointer select-none">
                        <input type="checkbox" id="select-all-chk" class="rounded border-neutral-border text-primary focus:ring-primary/30 cursor-pointer" checked
                            onchange="toggleSelectAll(this.checked)">
                        Pilih Semua
                    </label>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-neutral-surface border-b border-neutral-border">
                                <th class="px-4 py-3 w-10 text-center"></th>
                                <th class="px-4 py-3 text-left font-bold uppercase tracking-wider text-neutral-dark">#</th>
                                <th class="px-4 py-3 text-left font-bold uppercase tracking-wider text-neutral-dark">Status</th>
                                <th class="px-4 py-3 text-left font-bold uppercase tracking-wider text-neutral-dark">Nama Lengkap</th>
                                <th class="px-4 py-3 text-left font-bold uppercase tracking-wider text-neutral-dark">Email</th>
                                <th class="px-4 py-3 text-left font-bold uppercase tracking-wider text-neutral-dark">Peran</th>
                                <th class="px-4 py-3 text-left font-bold uppercase tracking-wider text-neutral-dark">No. HP</th>
                                <th class="px-4 py-3 text-left font-bold uppercase tracking-wider text-neutral-dark">Alamat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-border">
                            @foreach($candidates as $idx => $cand)
                                @php
                                    $isDuplicate = ($cand['status'] === 'DUPLICATE');
                                    $isError = ($cand['status'] === 'ERROR');
                                    $rowBg = $isDuplicate ? 'bg-accent-light/40' : ($isError ? 'bg-red-50/60' : 'hover:bg-neutral-surface/50');
                                @endphp
                                <tr class="{{ $rowBg }} transition-colors" id="cand-row-{{ $idx }}">
                                    {{-- Checkbox --}}
                                    <td class="px-4 py-3 text-center">
                                        @if(!$isDuplicate)
                                            <input type="checkbox" name="selected_ids[]" value="{{ $idx }}"
                                                class="candidate-chk rounded border-neutral-border text-primary focus:ring-primary/30 cursor-pointer"
                                                {{ !$isError ? 'checked' : '' }}>
                                        @else
                                            <div class="w-4 h-4 rounded border border-[#FDE68A] bg-accent-light mx-auto flex items-center justify-center">
                                                <svg class="w-2.5 h-2.5 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Index --}}
                                    <td class="px-4 py-3 font-mono text-neutral-muted">{{ $idx + 1 }}</td>

                                    {{-- Status Badge --}}
                                    <td class="px-4 py-3">
                                        @if($isDuplicate)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-accent-light text-accent border border-[#FDE68A]">DUPLIKAT</span>
                                        @elseif($isError)
                                            <div>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-danger border border-red-200">ERROR</span>
                                                <div class="mt-1 space-y-0.5">
                                                    @foreach($cand['errors'] ?? [] as $err)
                                                        <p class="text-[10px] text-danger">• {{ $err }}</p>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-success border border-green-200">SIAP</span>
                                        @endif
                                    </td>

                                    {{-- Editable fields --}}
                                    <td class="px-4 py-3">
                                        <input type="text" name="candidates[{{ $idx }}][name]"
                                            value="{{ $cand['name'] }}"
                                            class="w-full min-w-[160px] px-2.5 py-1.5 bg-white border border-neutral-border rounded text-xs text-neutral-dark focus:outline-none focus:ring-1 focus:ring-primary/30 focus:border-primary transition-all"
                                            placeholder="Nama lengkap" {{ $isDuplicate ? 'readonly' : '' }}>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="email" name="candidates[{{ $idx }}][email]"
                                            value="{{ $cand['email'] }}"
                                            class="w-full min-w-[180px] px-2.5 py-1.5 bg-white border border-neutral-border rounded text-xs text-neutral-dark focus:outline-none focus:ring-1 focus:ring-primary/30 focus:border-primary transition-all"
                                            placeholder="email@domain.com" {{ $isDuplicate ? 'readonly' : '' }}>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if(!$isDuplicate)
                                            <select name="candidates[{{ $idx }}][role]"
                                                class="px-2.5 py-1.5 bg-white border border-neutral-border rounded text-xs text-neutral-dark focus:outline-none focus:ring-1 focus:ring-primary/30 focus:border-primary">
                                                <option value="anggota" {{ ($cand['role'] ?? 'anggota') === 'anggota' ? 'selected' : '' }}>Anggota</option>
                                                <option value="admin" {{ ($cand['role'] ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
                                            </select>
                                        @else
                                            <span class="text-neutral-muted italic">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="candidates[{{ $idx }}][phone]"
                                            value="{{ $cand['phone'] }}"
                                            class="w-full min-w-[120px] px-2.5 py-1.5 bg-white border border-neutral-border rounded text-xs text-neutral-dark focus:outline-none focus:ring-1 focus:ring-primary/30 focus:border-primary transition-all"
                                            placeholder="Opsional" {{ $isDuplicate ? 'readonly' : '' }}>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="candidates[{{ $idx }}][address]"
                                            value="{{ $cand['address'] }}"
                                            class="w-full min-w-[180px] px-2.5 py-1.5 bg-white border border-neutral-border rounded text-xs text-neutral-dark focus:outline-none focus:ring-1 focus:ring-primary/30 focus:border-primary transition-all"
                                            placeholder="Opsional" {{ $isDuplicate ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Actions Footer --}}
                <div class="px-6 py-4 border-t border-neutral-border bg-neutral-surface flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-neutral-muted">
                        <span id="selected-count" class="font-bold text-neutral-dark">{{ $okCount }}</span> dari <strong>{{ $totalCandidates }}</strong> kandidat dipilih untuk diimport
                    </p>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.members.import.create') }}" class="btn-editorial-outline text-xs py-2 px-5">
                            Batal
                        </a>
                        <button type="submit"
                            class="btn-editorial text-xs py-2 px-6 flex items-center gap-2"
                            onclick="return confirmImport()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"></path>
                            </svg>
                            Import Semua yang Dipilih
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function toggleSelectAll(checked) {
            document.querySelectorAll('.candidate-chk').forEach(chk => {
                chk.checked = checked;
            });
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const total = document.querySelectorAll('.candidate-chk:checked').length;
            document.getElementById('selected-count').textContent = total;
        }

        document.querySelectorAll('.candidate-chk').forEach(chk => {
            chk.addEventListener('change', updateSelectedCount);
        });

        function confirmImport() {
            const count = document.querySelectorAll('.candidate-chk:checked').length;
            if (count === 0) {
                alert('Pilih minimal 1 kandidat anggota untuk diimport.');
                return false;
            }
            return confirm(`Anda akan mengimport ${count} akun anggota ke sistem. Lanjutkan?`);
        }
    </script>
</x-app-layout>
