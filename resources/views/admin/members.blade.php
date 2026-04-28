@extends('layouts.admin')
@section('title', 'Pelanggan')
@section('content')
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold">Pelanggan</h1>
                <p class="text-sm text-on-surface-variant">{{ $members->total() }} pelanggan terdaftar</p>
            </div>
            <button
                onclick="document.getElementById('modal-add').classList.remove('hidden'); document.getElementById('modal-add').classList.add('flex')"
                class="flex items-center gap-1 px-4 py-2 bg-primary-container text-white rounded-xl text-sm font-medium hover:bg-[#e08e0b]">
                <span class="material-symbols-outlined text-[18px]">add</span> Tambah
            </button>
        </div>

        <div class="bg-white rounded-2xl border border-outline-variant overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-surface-container text-on-surface-variant text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left hidden md:table-cell">No. HP</th>
                        <th class="px-4 py-3 text-center hidden md:table-cell">Order</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr class="border-t border-[#f0e0d2] hover:bg-surface-container/50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center text-white font-bold text-xs">
                                        {{ strtoupper(substr($member->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ $member->name }}</p>
                                        <p class="text-xs text-on-surface-variant md:hidden">{{ $member->phone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell text-on-surface-variant">{{ $member->phone }}</td>
                            <td class="px-4 py-3 text-center hidden md:table-cell">
                                <span
                                    class="px-2 py-0.5 bg-surface-container text-[#865300] rounded-full text-xs font-semibold">{{ $member->orders_count }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @php
                                    $waPhone = preg_replace('/\D+/', '', $member->phone ?? '');
                                    if (str_starts_with($waPhone, '0')) {
                                        $waPhone = '62' . ltrim($waPhone, '0');
                                    } elseif (!str_starts_with($waPhone, '62')) {
                                        $waPhone = '62' . $waPhone;
                                    }
                                @endphp
                                <div class="inline-flex items-center justify-end gap-1.5 flex-nowrap">
                                    <button type="button" data-id="{{ $member->id }}" data-name="{{ $member->name }}"
                                        data-phone="{{ $member->phone }}" data-address="{{ $member->address }}"
                                        data-email="{{ $member->email }}" onclick="openEditMember(this)"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-surface-container text-[#865300] hover:bg-surface-container-high transition-colors shrink-0">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                    </button>
                                    <a href="https://wa.me/{{ $waPhone }}" target="_blank"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition-colors shrink-0">
                                        <span class="material-symbols-outlined text-[16px]">chat</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.members.destroy', $member) }}"
                                        class="inline" onsubmit="return confirm('Hapus pelanggan ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition-colors shrink-0">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-on-surface-variant">Belum ada pelanggan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $members->links() }}</div>
    </div>

    <!-- Add Member Modal -->
    <div id="modal-add" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/40 p-4" x-data>
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-lg">Tambah Pelanggan</h3>
                <button
                    onclick="document.getElementById('modal-add').classList.add('hidden'); document.getElementById('modal-add').classList.remove('flex')"
                    class="text-on-surface-variant hover:text-red-500">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.members.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" required
                        class="w-full border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-container focus:border-primary-container">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1">No. WhatsApp *</label>
                    <div class="flex items-center border border-outline-variant rounded-xl overflow-hidden">
                        <span
                            class="px-3 py-2.5 text-sm bg-surface-container text-[#865300] font-semibold border-r border-outline-variant">62</span>
                        <input type="text" name="phone" required placeholder="8xx-xxxx-xxxx"
                            class="w-full px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-container focus:border-primary-container border-0">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Alamat</label>
                    <input type="text" name="address"
                        class="w-full border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-container focus:border-primary-container">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button"
                        onclick="document.getElementById('modal-add').classList.add('hidden'); document.getElementById('modal-add').classList.remove('flex')"
                        class="flex-1 py-2.5 border border-outline-variant rounded-xl text-sm text-on-surface-variant hover:bg-surface-container">Batal</button>
                    <button type="submit"
                        class="flex-1 py-2.5 bg-primary-container text-white rounded-xl text-sm font-semibold hover:bg-[#e08e0b]">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Member Modal -->
    <div id="modal-edit-member" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/40 p-4">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-lg">Edit Pelanggan</h3>
                <button type="button"
                    onclick="document.getElementById('modal-edit-member').classList.add('hidden'); document.getElementById('modal-edit-member').classList.remove('flex')"
                    class="text-on-surface-variant hover:text-red-500">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="form-edit-member" method="POST" action="#" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Nama Lengkap *</label>
                    <input id="edit-member-name" type="text" name="name" required
                        class="w-full border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-container focus:border-primary-container">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1">No. WhatsApp *</label>
                    <div class="flex items-center border border-outline-variant rounded-xl overflow-hidden">
                        <span
                            class="px-3 py-2.5 text-sm bg-surface-container text-[#865300] font-semibold border-r border-outline-variant">62</span>
                        <input id="edit-member-phone" type="text" name="phone" required
                            class="w-full px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-container focus:border-primary-container border-0">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Email</label>
                    <input id="edit-member-email" type="email" name="email"
                        class="w-full border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-container focus:border-primary-container">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Alamat</label>
                    <input id="edit-member-address" type="text" name="address"
                        class="w-full border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-container focus:border-primary-container">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button"
                        onclick="document.getElementById('modal-edit-member').classList.add('hidden'); document.getElementById('modal-edit-member').classList.remove('flex')"
                        class="flex-1 py-2.5 border border-outline-variant rounded-xl text-sm text-on-surface-variant hover:bg-surface-container">Batal</button>
                    <button type="submit"
                        class="flex-1 py-2.5 bg-primary-container text-white rounded-xl text-sm font-semibold hover:bg-[#e08e0b]">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditMember(button) {
            const d = button.dataset;
            let phoneDigits = (d.phone || '').replace(/\D/g, '');
            if (phoneDigits.startsWith('62')) {
                phoneDigits = phoneDigits.substring(2);
            } else if (phoneDigits.startsWith('0')) {
                phoneDigits = phoneDigits.substring(1);
            }

            document.getElementById('form-edit-member').action = `{{ url('/admin/members') }}/${d.id}`;
            document.getElementById('edit-member-name').value = d.name || '';
            document.getElementById('edit-member-phone').value = phoneDigits;
            document.getElementById('edit-member-email').value = d.email || '';
            document.getElementById('edit-member-address').value = d.address || '';
            document.getElementById('modal-edit-member').classList.remove('hidden');
            document.getElementById('modal-edit-member').classList.add('flex');
        }
    </script>
@endsection
