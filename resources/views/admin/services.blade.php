@extends('layouts.admin')
@section('title', 'Kelola Layanan')
@section('content')
<div class="max-w-4xl">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-xl font-bold">Layanan</h1>
      <p class="text-sm text-[#534434]">Kelola daftar layanan laundry</p>
    </div>
    <button onclick="document.getElementById('modal-add-svc').classList.remove('hidden')"
            class="flex items-center gap-1 px-4 py-2 bg-[#f39c12] text-white rounded-xl text-sm font-medium hover:bg-[#e08e0b]">
      <span class="material-symbols-outlined text-[18px]">add</span> Tambah
    </button>
  </div>

  <div class="space-y-3">
    @foreach($services->groupBy('category') as $cat => $items)
    <div class="bg-white rounded-2xl border border-[#d8c3ad] overflow-hidden">
      <div class="px-4 py-2 bg-[#fbebdd] border-b border-[#d8c3ad]">
        <span class="text-xs font-bold uppercase tracking-wide text-[#865300]">{{ ucfirst($cat) }}</span>
      </div>
      @foreach($items as $svc)
      <div class="flex items-center gap-3 px-4 py-3 border-b border-[#f0e0d2] last:border-0 hover:bg-[#fbebdd]/50">
        <div class="w-9 h-9 rounded-xl bg-[#fbebdd] flex items-center justify-center flex-shrink-0">
          <span class="material-symbols-outlined text-[#f39c12] text-[18px] filled">{{ $svc->icon }}</span>
        </div>
        <div class="flex-1">
          <p class="font-medium text-sm">{{ $svc->name }}</p>
          <p class="text-xs text-[#534434]">Rp {{ number_format($svc->price, 0, ',', '.') }}/{{ $svc->unit }} · {{ $svc->type }}</p>
        </div>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $svc->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
          {{ $svc->is_active ? 'Aktif' : 'Nonaktif' }}
        </span>
        <form method="POST" action="{{ route('admin.services.destroy', $svc) }}" onsubmit="return confirm('Hapus layanan ini?')">
          @csrf @method('DELETE')
          <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors">
            <span class="material-symbols-outlined text-[18px]">delete</span>
          </button>
        </form>
      </div>
      @endforeach
    </div>
    @endforeach
  </div>
</div>

<!-- Add Service Modal -->
<div id="modal-add-svc" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
    <div class="flex items-center justify-between mb-5">
      <h3 class="font-bold text-lg">Tambah Layanan</h3>
      <button onclick="document.getElementById('modal-add-svc').classList.add('hidden')" class="text-[#534434] hover:text-red-500">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <form method="POST" action="{{ route('admin.services.store') }}" class="space-y-4">
      @csrf
      <div class="grid grid-cols-2 gap-3">
        <div class="col-span-2">
          <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Nama Layanan *</label>
          <input type="text" name="name" required class="w-full border border-[#d8c3ad] rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#f39c12]">
        </div>
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Kategori *</label>
          <select name="category" required class="w-full border border-[#d8c3ad] rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#f39c12]">
            <option value="kiloan">Kiloan</option>
            <option value="satuan">Satuan</option>
            <option value="sepatu">Sepatu</option>
            <option value="setrika">Setrika</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Tipe *</label>
          <select name="type" required class="w-full border border-[#d8c3ad] rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#f39c12]">
            <option value="kiloan">Kiloan</option>
            <option value="satuan">Satuan</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Harga (Rp) *</label>
          <input type="number" name="price" min="0" required class="w-full border border-[#d8c3ad] rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#f39c12]">
        </div>
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Satuan *</label>
          <input type="text" name="unit" value="pcs" required class="w-full border border-[#d8c3ad] rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#f39c12]">
        </div>
        <div class="col-span-2">
          <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Icon (Material Symbols)</label>
          <input type="text" name="icon" value="local_laundry_service" class="w-full border border-[#d8c3ad] rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#f39c12]">
        </div>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('modal-add-svc').classList.add('hidden')"
                class="flex-1 py-2.5 border border-[#d8c3ad] rounded-xl text-sm text-[#534434] hover:bg-[#fbebdd]">Batal</button>
        <button type="submit" class="flex-1 py-2.5 bg-[#f39c12] text-white rounded-xl text-sm font-semibold hover:bg-[#e08e0b]">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection