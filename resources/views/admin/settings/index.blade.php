<x-app-layout>
    <x-slot name="header">
        Pengaturan Kebijakan Sistem
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Sidebar Institutional Info -->
            <div class="md:col-span-1 space-y-5">
                <div class="bg-primary rounded-lg p-6 text-white shadow-xs border border-primary-dark">
                    <div class="w-10 h-10 bg-white/15 rounded-md flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-serif text-lg font-semibold mb-1">Tata Tertib Sirkulasi</h3>
                    <p class="text-white/80 text-xs leading-relaxed">Konfigurasi parameter operasional denda keterlambatan untuk menjaga kedisiplinan peredaran koleksi fisik perpustakaan.</p>
                </div>

                <div class="bg-white rounded-lg p-5 border border-neutral-border shadow-xs space-y-2">
                    <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block">Catatan Kebijakan</span>
                    <p class="text-xs text-neutral-body leading-relaxed italic">
                        "Nominal denda keterlambatan berlaku harian dan terakumulasi otomatis saat buku diserahkan kembali oleh anggota melewati tanggal jatuh tempo."
                    </p>
                </div>
            </div>

            <!-- Settings Form -->
            <div class="md:col-span-2">
                <x-card>
                    <x-slot name="header">Konfigurasi Denda & Keuangan</x-slot>

                    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="space-y-5">
                            <div class="bg-neutral-surface p-5 rounded-md border border-neutral-border space-y-3">
                                <x-input 
                                    label="Denda Keterlambatan Per Hari (Rupiah)" 
                                    name="late_fine_per_day" 
                                    type="number" 
                                    placeholder="Contoh: 1000"
                                    value="{{ old('late_fine_per_day', $settings['late_fine_per_day'] ?? 1000) }}"
                                    required
                                    min="0"
                                    :error="$errors->first('late_fine_per_day')"
                                />
                                <p class="text-[11px] text-neutral-muted italic px-0.5">
                                    * Tarif harian ini otomatis dikalikan dengan selisih hari keterlambatan saat petugas memproses pengembalian buku di sistem.
                                </p>
                            </div>

                            <div class="bg-neutral-surface p-5 rounded-md border border-neutral-border space-y-3">
                                <x-input 
                                    label="Durasi Standar Peminjaman Digital (Hari)" 
                                    name="digital_loan_duration_days" 
                                    type="number" 
                                    placeholder="Contoh: 7"
                                    value="{{ old('digital_loan_duration_days', $settings['digital_loan_duration_days'] ?? 7) }}"
                                    required
                                    min="1"
                                    max="90"
                                    :error="$errors->first('digital_loan_duration_days')"
                                />
                                <p class="text-[11px] text-neutral-muted italic px-0.5">
                                    * Masa berlaku hak akses pembaca naskah digital (e-book) bagi anggota melalui website.
                                </p>
                            </div>

                            <div class="bg-neutral-surface p-5 rounded-md border border-neutral-border space-y-3">
                                <x-input 
                                    label="Batas Maksimal Peminjaman Buku Fisik (Hari)" 
                                    name="physical_loan_duration_days" 
                                    type="number" 
                                    placeholder="Contoh: 14"
                                    value="{{ old('physical_loan_duration_days', $settings['physical_loan_duration_days'] ?? 14) }}"
                                    required
                                    min="1"
                                    max="90"
                                    :error="$errors->first('physical_loan_duration_days')"
                                />
                                <p class="text-[11px] text-neutral-muted italic px-0.5">
                                    * Durasi maksimal kalender yang diizinkan untuk peminjaman eksemplar fisik buku perpustakaan.
                                </p>
                            </div>

                            <div class="pt-4 border-t border-neutral-border">
                                <x-button type="submit" variant="primary" class="w-full text-xs uppercase tracking-wider font-semibold py-3">
                                    Simpan Perubahan Kebijakan
                                </x-button>
                            </div>
                        </div>
                    </form>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
