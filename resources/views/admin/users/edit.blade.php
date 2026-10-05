<x-app-layout>
    <x-slot name="header">
        Perbarui Data Pengguna
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
            <x-slot name="header">Formulir Pembaruan Pengguna</x-slot>

            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Profile Header Banner -->
                <div class="flex items-center space-x-4 p-4 bg-neutral-surface rounded-md border border-neutral-border">
                    <div class="w-12 h-12 bg-primary-light text-primary border border-primary/20 rounded-md flex items-center justify-center font-serif text-lg font-bold">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="font-serif text-base font-semibold text-neutral-dark">{{ $user->name }}</h3>
                        <p class="text-xs text-neutral-body">{{ $user->email }}</p>
                        <x-badge variant="{{ $user->role === 'admin' ? 'indigo' : 'slate' }}" class="mt-1">
                            {{ $user->role === 'admin' ? 'Admin' : 'Anggota' }}
                        </x-badge>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <x-input 
                        label="Nama Lengkap" 
                        name="name" 
                        :value="old('name', $user->name)" 
                        required 
                        :error="$errors->first('name')" 
                    />

                    <x-input 
                        label="Alamat Email" 
                        type="email" 
                        name="email" 
                        :value="old('email', $user->email)" 
                        required 
                        :error="$errors->first('email')" 
                    />

                    <div>
                        <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider mb-2 px-0.5">Peran / Hak Akses</label>
                        <select name="role" required
                            class="w-full px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                            <option value="anggota" {{ old('role', $user->role) == 'anggota' ? 'selected' : '' }}>Anggota</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-1.5" />
                    </div>

                    <x-input 
                        label="Nomor Telepon / WhatsApp" 
                        name="phone" 
                        :value="old('phone', $user->phone)" 
                        :error="$errors->first('phone')" 
                    />

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider mb-2 px-0.5">Alamat Domisili</label>
                        <textarea name="address" rows="2" 
                            class="w-full px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark placeholder:text-neutral-muted focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">{{ old('address', $user->address) }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-1.5" />
                    </div>

                    <div class="md:col-span-2 pt-2 border-t border-neutral-border">
                        <p class="text-xs font-semibold text-neutral-dark uppercase tracking-wider mb-3">Ubah Kata Sandi (Opsional)</p>
                        <div class="grid md:grid-cols-2 gap-4">
                            <x-input 
                                label="Kata Sandi Baru" 
                                type="password" 
                                name="password" 
                                placeholder="Kosongkan jika tidak diubah" 
                                :error="$errors->first('password')" 
                            />
                            <x-input 
                                label="Ulangi Sandi Baru" 
                                type="password" 
                                name="password_confirmation" 
                                placeholder="Ulangi sandi baru" 
                            />
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-neutral-border flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.users.index') }}" class="btn-editorial-outline text-xs py-2 px-4 uppercase tracking-wider">Batal</a>
                    <x-button type="submit" variant="primary" class="text-xs py-2 px-6 uppercase tracking-wider font-semibold">Simpan Perubahan</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
