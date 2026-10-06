@extends('layouts.auth', ['title' => 'Atur Ulang Kata Sandi'])

@section('content')
<div class="space-y-8 animate-in fade-in duration-300">
    <div class="space-y-2">
        <div class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-accent fill-current" viewBox="0 0 24 24">
                <path d="M12 2l2.4 7.2h7.6l-6.2 4.5 2.4 7.3-6.2-4.6-6.2 4.6 2.4-7.3-6.2-4.5h7.6z"/>
            </svg>
            <span class="text-xs uppercase tracking-[0.2em] font-semibold text-primary block">Keamanan Akun</span>
        </div>
        <h1 class="font-sans text-2xl sm:text-3xl font-bold text-neutral-dark tracking-tight leading-tight">Atur Ulang Kata Sandi</h1>
        <p class="text-sm text-neutral-body">Silakan masukkan kata sandi baru Anda untuk memulihkan akses ke akun perpustakaan.</p>
    </div>

    @if (session('status'))
        <div class="p-3 bg-green-50 border border-green-200 text-success text-xs font-semibold rounded-md">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-input 
            label="Alamat Surel" 
            type="email" 
            name="email" 
            id="email" 
            :value="old('email', $request->email)" 
            required 
            autofocus 
            autocomplete="username"
            placeholder="nama@email.com"
            :error="$errors->first('email')"
        />

        <x-input 
            label="Kata Sandi Baru" 
            type="password" 
            name="password" 
            id="password" 
            required 
            autocomplete="new-password"
            placeholder="••••••••"
            :error="$errors->first('password')"
        />

        <x-input 
            label="Konfirmasi Kata Sandi Baru" 
            type="password" 
            name="password_confirmation" 
            id="password_confirmation" 
            required 
            autocomplete="new-password"
            placeholder="••••••••"
        />

        <div class="pt-2">
            <x-button class="w-full py-3 uppercase tracking-wider text-xs font-semibold" type="submit" variant="primary">
                Perbarui Kata Sandi
            </x-button>
        </div>
    </form>

    <div class="text-center pt-4 border-t border-neutral-border">
        <a href="{{ route('login') }}" class="text-xs font-semibold text-primary hover:text-primary-hover hover:underline inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Halaman Masuk</span>
        </a>
    </div>
</div>
@endsection

