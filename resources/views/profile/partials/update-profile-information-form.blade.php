<section>
    <header class="mb-6">
        <h3 class="text-lg font-sans font-bold text-neutral-dark">
            Data Akun & Afiliasi
        </h3>
        <p class="mt-1 text-xs text-neutral-muted leading-relaxed">
            Perbarui nama lengkap dan alamat surel resmi yang digunakan untuk notifikasi sirkulasi peminjaman koleksi RPK PUSTAKA IMM SAINTEKMU.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap Sesuai Identitas')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Alamat Surel (Email)')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-[#F8F8F7] border border-neutral-border rounded">
                    <p class="text-xs text-neutral-dark">
                        Alamat surel Anda belum diverifikasi secara resmi.
                        <button form="send-verification" class="underline text-xs text-primary hover:text-primary-hover font-medium ml-1">
                            Kirim ulang surel verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-xs font-mono text-success">
                            Tautan verifikasi baru telah dikirimkan ke alamat surel Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-xs font-mono text-primary"
                >Tersimpan.</p>
            @endif
        </div>
    </form>
</section>
