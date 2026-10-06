@extends('layouts.auth', ['title' => 'Verifikasi Email'])

@section('content')
<div class="space-y-8 animate-in fade-in duration-300">
    <div class="space-y-2">
        <div class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-accent fill-current" viewBox="0 0 24 24">
                <path d="M12 2l2.4 7.2h7.6l-6.2 4.5 2.4 7.3-6.2-4.6-6.2 4.6 2.4-7.3-6.2-4.5h7.6z"/>
            </svg>
            <span class="text-xs uppercase tracking-[0.2em] font-semibold text-primary block">Verifikasi Akun</span>
        </div>
        <h1 class="font-sans text-2xl sm:text-3xl font-bold text-neutral-dark tracking-tight leading-tight">Verifikasi Email Anda</h1>
        <p class="text-sm text-neutral-body">Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat surel Anda dengan menekan tautan yang telah kami kirimkan.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="p-3 bg-green-50 border border-green-200 text-success text-xs font-semibold rounded-md">
            Tautan verifikasi baru telah dikirimkan ke alamat email yang Anda daftarkan.
        </div>
    @endif

    <div class="space-y-4 pt-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-button class="w-full py-3 uppercase tracking-wider text-xs font-semibold" type="submit" variant="primary">
                Kirim Ulang Email Verifikasi
            </x-button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center pt-2">
            @csrf
            <button type="submit" class="text-xs font-semibold text-neutral-muted hover:text-danger transition-colors underline">
                Keluar dari Sesi
            </button>
        </form>
    </div>
</div>
@endsection

