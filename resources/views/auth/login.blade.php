@extends('layouts.auth', ['title' => 'Masuk'])

@section('content')
<div class="space-y-8 animate-in fade-in duration-300">
    <div class="space-y-2">
        <div class="flex items-center gap-1.5">
            <!-- Small Gold Star Accent from Logo -->
            <svg class="w-3.5 h-3.5 text-accent fill-current" viewBox="0 0 24 24">
                <path d="M12 2l2.4 7.2h7.6l-6.2 4.5 2.4 7.3-6.2-4.6-6.2 4.6 2.4-7.3-6.2-4.5h7.6z"/>
            </svg>
            <span class="text-xs uppercase tracking-[0.2em] font-semibold text-primary block">Portal Anggota</span>
        </div>
        <h1 class="font-sans text-2xl sm:text-3xl font-bold text-neutral-dark tracking-tight leading-tight">Selamat Datang</h1>
        <p class="text-sm text-neutral-body">Masuk ke akun RPK PUSTAKA IMM SAINTEK MU untuk mengakses katalog, reservasi, dan riwayat sirkulasi.</p>
    </div>

    @if (session('status'))
        <div class="p-3 bg-green-50 border border-green-200 text-success text-xs font-semibold rounded-md">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-3 bg-red-50 border border-red-200 text-danger text-xs font-semibold rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <x-input 
            label="Alamat Surel" 
            type="email" 
            name="email" 
            id="email" 
            :value="old('email')" 
            required 
            autofocus 
            placeholder="nama@email.com"
            :error="$errors->first('email')"
        />

        <div class="space-y-1.5">
            <x-input 
                label="Kata Sandi" 
                type="password" 
                name="password" 
                id="password" 
                required 
                placeholder="••••••••"
                :error="$errors->first('password')"
            />
            @if (Route::has('password.request'))
                <div class="flex justify-end pt-1">
                    <a class="text-xs font-medium text-primary hover:text-primary-hover hover:underline transition-colors" href="{{ route('password.request') }}">
                        Lupa kata sandi?
                    </a>
                </div>
            @endif
        </div>

        <div class="flex items-center pt-1">
            <input id="remember_me" type="checkbox" class="rounded border-neutral-border text-primary shadow-xs focus:ring-primary/20 cursor-pointer" name="remember">
            <label for="remember_me" class="block text-xs font-medium text-neutral-body ml-2.5 cursor-pointer">
                Ingat sesi saya di perangkat ini
            </label>
        </div>

        <div class="pt-2">
            <x-button class="w-full py-3 uppercase tracking-wider text-xs font-semibold" type="submit" variant="primary">
                Masuk ke Perpustakaan
            </x-button>
        </div>
    </form>

    <!-- Alternative Login Divider -->
    <div class="relative flex py-1 items-center">
        <div class="flex-grow border-t border-neutral-border"></div>
        <span class="flex-shrink mx-4 text-[11px] font-medium text-neutral-muted uppercase tracking-wider">atau</span>
        <div class="flex-grow border-t border-neutral-border"></div>
    </div>

    <!-- Google Login Button -->
    <div>
        <a href="{{ route('auth.google') }}" 
           class="w-full py-3 px-4 bg-white hover:bg-neutral-surface text-neutral-dark border border-neutral-border hover:border-neutral-dark/30 rounded-md text-xs font-semibold uppercase tracking-wider shadow-xs transition-all flex items-center justify-center gap-3 group">
            <svg class="w-4 h-4 shrink-0 transition-transform group-hover:scale-110" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>Lanjutkan dengan Google</span>
        </a>
    </div>

    <div class="text-center pt-4 border-t border-neutral-border">
        <p class="text-xs text-neutral-body">
            Belum terdaftar sebagai anggota? 
            <a href="{{ route('register') }}" class="text-primary hover:text-primary-hover font-semibold hover:underline ml-1">Daftar Sekarang</a>
        </p>
    </div>
</div>
@endsection
