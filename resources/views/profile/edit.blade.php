<x-app-layout>
    <x-slot name="header">
        Pengaturan Profil & Keamanan
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6 animate-in fade-in duration-300">
        
        <!-- Profile Identity & Role Banner Card -->
        <div class="bg-white p-5 rounded-lg border border-neutral-border shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1.5">
                <nav class="flex items-center gap-2 text-[11px] text-neutral-muted uppercase tracking-wider font-mono">
                    <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dasbor</a>
                    <span>/</span>
                    <span class="text-primary font-bold">Pengaturan Akun</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-xl sm:text-2xl font-bold text-neutral-dark tracking-tight leading-snug">
                        {{ $user->name }}
                    </h2>
                    @if($user->isAdmin())
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold bg-primary-light text-primary border border-red-200 rounded">
                            Administrator Utama
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold bg-[#FFF9ED] text-[#B45309] border border-[#FDE68A] rounded">
                            Anggota Perpustakaan
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4 text-xs font-mono text-neutral-muted pt-3 sm:pt-0 border-t sm:border-t-0 border-neutral-border">
                <div>
                    <span class="text-neutral-muted block text-[10px] uppercase tracking-wider">Alamat Surel</span>
                    <span class="font-bold text-neutral-dark">{{ $user->email }}</span>
                </div>
                <div class="w-px h-6 bg-neutral-border hidden sm:block"></div>
                <div>
                    <span class="text-neutral-muted block text-[10px] uppercase tracking-wider">Terdaftar Sejak</span>
                    <span class="font-bold text-neutral-dark">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Profile Information Section -->
        <div class="bg-white border border-neutral-border rounded-lg overflow-hidden shadow-xs">
            <div class="px-6 py-4 border-b border-neutral-border bg-[#F8F8F7] flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-primary"></span>
                <h2 class="text-xs font-bold text-neutral-dark uppercase tracking-wider">
                    Informasi Profil {{ $user->isAdmin() ? 'Administrator' : 'Anggota' }}
                </h2>
            </div>
            <div class="p-6 sm:p-8">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Security / Password Section -->
        <div class="bg-white border border-neutral-border rounded-lg overflow-hidden shadow-xs">
            <div class="px-6 py-4 border-b border-neutral-border bg-[#F8F8F7] flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-accent"></span>
                <h2 class="text-xs font-bold text-neutral-dark uppercase tracking-wider">
                    Pembaruan Kata Sandi
                </h2>
            </div>
            <div class="p-6 sm:p-8">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Danger Zone / Account Deletion Section -->
        <div class="bg-white border border-neutral-border rounded-lg overflow-hidden shadow-xs">
            <div class="px-6 py-4 border-b border-neutral-border bg-[#F8F8F7] flex items-center gap-2">
                <span class="w-2 h-2 rounded-full {{ $user->isAdmin() ? 'bg-primary' : 'bg-danger' }}"></span>
                <h2 class="text-xs font-bold {{ $user->isAdmin() ? 'text-neutral-dark' : 'text-danger' }} uppercase tracking-wider">
                    {{ $user->isAdmin() ? 'Proteksi Akun Administrator' : 'Zona Pembatalan Akun Anggota' }}
                </h2>
            </div>
            <div class="p-6 sm:p-8">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

    </div>
</x-app-layout>
