<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-serif font-medium text-[#120504]">Pemulihan Kata Sandi</h2>
        <p class="mt-2 text-xs text-[#8A7A70] leading-relaxed">
            Masukkan alamat surel yang terdaftar pada sistem perpustakaan. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Alamat Surel')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center">
                {{ __('Kirim Tautan Pemulihan') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
