<x-app-layout>
    <x-slot name="header">
        @if(request('role') == 'anggota')
            Manajemen Anggota Perpustakaan
        @elseif(request('role') == 'admin')
            Manajemen Administrator
        @else
            Seluruh Pengguna Sistem
        @endif
    </x-slot>

    <x-slot name="actions">
        <div class="flex items-center gap-2">
            @if(request('role') === 'anggota' || !request('role'))
                <a href="{{ route('admin.members.import.create') }}" class="btn-editorial-outline text-xs py-2 px-4 shadow-xs flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"></path>
                    </svg>
                    Import Massal
                </a>
            @endif
            <a href="{{ route('admin.users.create', ['role' => request('role')]) }}" class="btn-editorial text-xs py-2 px-4 shadow-xs">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Pengguna Baru
            </a>
        </div>
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300">
        <!-- Search & Filter Toolbar -->
        <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs">
            <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 items-end">
                <input type="hidden" name="role" value="{{ request('role') }}">

                <div class="flex-1 w-full space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-dark uppercase tracking-wider block px-0.5">
                        Pencarian Nama atau Alamat Email
                    </label>
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Ketik nama atau email anggota..."
                            class="w-full pl-10 pr-4 py-2 bg-neutral-surface border border-neutral-border rounded-md text-sm text-neutral-dark placeholder:text-neutral-muted focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    </div>
                </div>

                @if(!request('role'))
                    <div class="w-full md:w-48 space-y-1.5">
                        <label class="text-[11px] font-semibold text-neutral-dark uppercase tracking-wider block px-0.5">
                            Filter Hak Akses
                        </label>
                        <select name="role_filter" onchange="this.form.role.value = this.value; if (window.performLiveSearch) { window.performLiveSearch(this.form); } else { this.form.submit(); }"
                            class="w-full px-3.5 py-2 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                            <option value="">Semua Peran</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="anggota" {{ request('role') == 'anggota' ? 'selected' : '' }}>Anggota</option>
                        </select>
                    </div>
                @endif

                <div class="flex space-x-2">
                    <x-button type="submit" variant="primary" class="px-5 text-xs uppercase tracking-wider font-semibold">Cari</x-button>
                    @if(request('search'))
                        <a href="{{ route('admin.users.index', ['role' => request('role')]) }}" class="btn-editorial-outline px-4 text-xs uppercase tracking-wider font-semibold">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Academic Table View -->
        <div class="space-y-3">
            <div class="flex items-center justify-between px-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-neutral-dark">
                    Total Terdata: {{ $users->total() }} Pengguna
                </span>
            </div>

            <x-table :headers="['Identitas Pengguna', 'Hak Akses', 'Kontak & Domisili', 'Aksi']">
                @forelse($users as $user)
                    <tr class="hover:bg-neutral-surface transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3.5">
                                <div class="w-9 h-9 bg-primary-light border border-primary/20 text-primary font-serif font-bold text-sm rounded-md flex items-center justify-center uppercase shrink-0 shadow-xs">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <span class="font-serif text-sm font-semibold text-neutral-dark block leading-tight">{{ $user->name }}</span>
                                    <span class="text-[11px] text-neutral-body block mt-0.5">{{ $user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->role === 'admin')
                                <x-badge variant="indigo">ADMINISTRATOR</x-badge>
                            @else
                                <x-badge variant="slate">ANGGOTA</x-badge>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs space-y-1">
                                <div class="flex items-center space-x-1.5 text-neutral-dark">
                                    <svg class="w-3.5 h-3.5 text-neutral-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    <span>{{ $user->phone ?? '-' }}</span>
                                </div>
                                <p class="text-[11px] text-neutral-body truncate max-w-[260px]">{{ $user->address ?? 'Alamat belum diatur' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center space-x-1.5 justify-end">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                    class="p-1.5 text-neutral-body hover:text-primary hover:bg-primary-light rounded transition-colors"
                                    title="Edit Pengguna">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                @if(Auth::id() !== $user->id)
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline"
                                        data-confirm-message="Apakah Anda yakin ingin menghapus akun pengguna '{{ $user->name }}'? Data transaksi peminjaman terkait mungkin akan terpengaruh.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-1.5 text-neutral-muted hover:text-danger hover:bg-red-50 rounded transition-colors cursor-pointer"
                                            title="Hapus Pengguna">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-xs text-neutral-muted italic">
                            Tidak ada pengguna ditemukan.
                        </td>
                    </tr>
                @endforelse
            </x-table>
        </div>

        <div class="pt-4">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</x-app-layout>