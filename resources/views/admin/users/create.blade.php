<x-app-layout>
    <x-slot name="header">
        Registrasi Pengguna Baru
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300">
        <div>
            <a href="{{ route('admin.users.index') }}" 
               class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-neutral-body hover:text-primary transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Manajemen Pengguna
            </a>
        </div>

        <x-card>
            <x-slot name="header">Formulir Data Anggota / Petugas</x-slot>

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid md:grid-cols-2 gap-6">
                    <x-input 
                        label="Nama Lengkap" 
                        name="name" 
                        :value="old('name')" 
                        placeholder="Nama Lengkap" 
                        required 
                        :error="$errors->first('name')" 
                    />

                    <x-input 
                        label="Alamat Email" 
                        type="email" 
                        name="email" 
                        :value="old('email')" 
                        placeholder="email@domain.com" 
                        required 
                        :error="$errors->first('email')" 
                    />

                    <div>
                        <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider mb-2 px-0.5">Hak Akses (Peran)</label>
                        <select name="role" required
                            class="w-full px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                            <option value="anggota" {{ old('role', 'anggota') == 'anggota' ? 'selected' : '' }}>Anggota</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-1.5" />
                    </div>

                    <x-input 
                        label="Nomor Telepon / WhatsApp" 
                        name="phone" 
                        :value="old('phone')" 
                        placeholder="08xxxxxxxxxx" 
                        :error="$errors->first('phone')" 
                    />

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider mb-2 px-0.5">Alamat Domisili</label>
                        <textarea name="address" rows="2" placeholder="Alamat lengkap domisili..."
                            class="w-full px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark placeholder:text-neutral-muted focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">{{ old('address') }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-1.5" />
                    </div>

                    <x-input 
                        label="Kata Sandi Awal" 
                        type="password" 
                        name="password" 
                        required 
                        placeholder="••••••••" 
                        :error="$errors->first('password')" 
                    />

                    <x-input 
                        label="Konfirmasi Kata Sandi" 
                        type="password" 
                        name="password_confirmation" 
                        required 
                        placeholder="••••••••" 
                    />
                </div>

                <div class="pt-6 border-t border-neutral-border flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.users.index') }}" class="btn-editorial-outline text-xs py-2 px-4 uppercase tracking-wider">Batal</a>
                    <x-button type="submit" variant="primary" class="text-xs py-2 px-6 uppercase tracking-wider font-semibold">Simpan Pengguna</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
