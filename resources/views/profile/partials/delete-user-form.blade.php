<section class="space-y-6">
    @if(auth()->user()->isAdmin())
        <div class="flex items-start gap-4 p-5 rounded-lg bg-[#F8F8F7] border border-neutral-border">
            <div class="w-10 h-10 rounded-full bg-primary-light border border-red-200 flex items-center justify-center text-primary shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <div class="space-y-1">
                <h4 class="font-bold text-sm text-neutral-dark">Akun Administrator Utama Dilindungi</h4>
                <p class="text-xs text-neutral-body leading-relaxed">
                    Akun dengan peran <strong>Administrator Pengelola Pustaka</strong> diproteksi dan tidak dapat dihapus secara mandiri demi memastikan kontinuitas operasional, integritas basis data katalog, dan riwayat sirkulasi perpustakaan.
                </p>
                <div class="pt-2 text-[11px] font-mono text-neutral-muted">
                    <span>Manajemen akun pengguna lainnya dapat dikelola melalui menu </span>
                    <a href="{{ route('admin.users.index') }}" class="text-primary font-bold underline hover:text-primary-dark">
                        Manajemen Pengguna
                    </a>.
                </div>
            </div>
        </div>
    @else
        <header class="mb-4">
            <h3 class="text-base font-bold text-danger">
                Penghapusan Akun Anggota
            </h3>
            <p class="mt-1 text-xs text-neutral-body leading-relaxed">
                Setelah akun Anda dihapus secara permanen, seluruh catatan sirkulasi, preferensi, dan hak akses arsip Anda akan dinonaktifkan secara permanen.
            </p>
        </header>

        <x-danger-button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >{{ __('Hapus Akun Permanen') }}</x-danger-button>

        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
            <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8">
                @csrf
                @method('delete')

                <h3 class="text-lg font-bold text-neutral-dark">
                    Konfirmasi Penghapusan Akun
                </h3>

                <p class="mt-2 text-xs text-neutral-body leading-relaxed">
                    Tindakan ini tidak dapat dibatalkan. Masukkan kata sandi akun Anda untuk memvalidasi permintaan penghapusan akun dari sistem perpustakaan.
                </p>

                <div class="mt-6">
                    <x-input-label for="password" value="{{ __('Kata Sandi Konfirmasi') }}" class="sr-only" />

                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-full sm:w-3/4"
                        placeholder="{{ __('Masukkan Kata Sandi Anda') }}"
                    />

                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-neutral-border">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Batal') }}
                    </x-secondary-button>

                    <x-danger-button>
                        {{ __('Ya, Hapus Akun') }}
                    </x-danger-button>
                </div>
            </form>
        </x-modal>
    @endif
</section>
