@extends('layouts.admin')
@section('title', 'Pelanggan')
@section('content')
<div class="max-w-4xl">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-xl font-bold">Pelanggan</h1>
      <p class="text-sm text-[#534434]">{{ $members->total() }} pelanggan terdaftar</p>
    </div>
    <button onclick="document.getElementById('modal-add').classList.remove('hidden')"
            class="flex items-center gap-1 px-4 py-2 bg-[#f39c12] text-white rounded-xl text-sm font-medium hover:bg-[#e08e0b]">
      <span class="material-symbols-outlined text-[18px]">add</span> Tambah
    </button>
  </div>

  <div class="bg-white rounded-2xl border border-[#d8c3ad] overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-[#fbebdd] text-[#534434] text-xs uppercase">
        <tr>
          <th class="px-4 py-3 text-left">Nama</th>
          <th class="px-4 py-3 text-left hidden md:table-cell">No. HP</th>
          <th class="px-4 py-3 text-center hidden md:table-cell">Order</th>
          <th class="px-4 py-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($members as $member)
        <tr class="border-t border-[#f0e0d2] hover:bg-[#fbebdd]/50">
          <td class="px-4 py-3">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full bg-[#f39c12] flex items-center justify-center text-white font-bold text-xs">
                {{ strtoupper(substr($member->name, 0, 2)) }}
              </div>
              <div>
                <p class="font-medium">{{ $member->name }}</p>
                <p class="text-xs text-[#534434] md:hidden">{{ $member->phone }}</p>
              </div>
            </div>
          </td>
          <td class="px-4 py-3 hidden md:table-cell text-[#534434]">{{ $member->phone }}</td>
          <td class="px-4 py-3 text-center hidden md:table-cell">
            <span class="px-2 py-0.5 bg-[#fbebdd] text-[#865300] rounded-full text-xs font-semibold">{{ $member->orders_count }}</span>
          </td>
          <td class="px-4 py-3 text-right">
            <a href="https://wa.me/62{{ ltrim($member->phone, '0') }}" target="_blank"
               class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition-colors mr-1">
              <span class="material-symbols-outlined text-[16px]">chat</span>
            </a>
          </td>
        </tr>
        @empty
        <tr><td colspan="4" class="px-4 py-8 text-center text-[#534434]">Belum ada pelanggan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-4">{{ $members->links() }}</div>
</div>

<!-- Add Member Modal -->
<div id="modal-add" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" x-data>
  <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
    <div class="flex items-center justify-between mb-5">
      <h3 class="font-bold text-lg">Tambah Pelanggan</h3>
      <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-[#534434] hover:text-red-500">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <form method="POST" action="{{ route('admin.members.store') }}" class="space-y-4">
      @csrf
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Nama Lengkap *</label>
        <input type="text" name="name" required class="w-full border border-[#d8c3ad] rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#f39c12] focus:border-[#f39c12]">
      </div>
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wide mb-1">No. WhatsApp *</label>
        <input type="text" name="phone" required placeholder="08xx-xxxx-xxxx" class="w-full border border-[#d8c3ad] rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#f39c12] focus:border-[#f39c12]">
      </div>
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Alamat</label>
        <input type="text" name="address" class="w-full border border-[#d8c3ad] rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#f39c12] focus:border-[#f39c12]">
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('modal-add').classList.add('hidden')"
                class="flex-1 py-2.5 border border-[#d8c3ad] rounded-xl text-sm text-[#534434] hover:bg-[#fbebdd]">Batal</button>
        <button type="submit" class="flex-1 py-2.5 bg-[#f39c12] text-white rounded-xl text-sm font-semibold hover:bg-[#e08e0b]">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection