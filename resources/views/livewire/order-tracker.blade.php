<div>
  <div class="flex items-center justify-between mb-3">
    <div class="flex items-center gap-2">
      <span class="material-symbols-outlined text-[#f39c12] text-[20px]">manage_search</span>
      <span class="font-semibold text-sm">Lacak Order</span>
    </div>
    <span class="text-xs bg-[#fbebdd] text-[#865300] px-2 py-0.5 rounded-full font-medium">CEPAT</span>
  </div>

  <div class="space-y-2">
    <div class="relative">
      <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#534434] text-[18px]">tag</span>
      <input wire:model="query"
             wire:keydown.enter="track"
             type="text"
             placeholder="Nomor Order atau No. HP"
             class="w-full pl-10 pr-4 py-3 border border-[#d8c3ad] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#f39c12] focus:border-[#f39c12] bg-[#fff8f4]">
    </div>
    <button wire:click="track" wire:loading.attr="disabled"
            class="w-full py-3 bg-[#865300] hover:bg-[#6d4400] text-white font-semibold rounded-xl text-sm flex items-center justify-center gap-2 transition-all active:scale-[0.98]">
      <span wire:loading.remove>Cek Status →</span>
      <span wire:loading class="flex items-center gap-2">
        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="white" stroke-width="3" opacity="0.3"/><path d="M12 2a10 10 0 0110 10" stroke="white" stroke-width="3" stroke-linecap="round"/></svg>
        Mencari...
      </span>
    </button>
  </div>

  @if($errorMsg)
  <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600 flex items-center gap-2">
    <span class="material-symbols-outlined text-[16px]">error</span>
    {{ $errorMsg }}
  </div>
  @endif

  @if($result)
  <div class="mt-4 p-4 bg-[#fbebdd] rounded-xl border border-[#d8c3ad]">
    <div class="flex items-center justify-between mb-3">
      <span class="font-bold text-sm">{{ $result->order_number }}</span>
      <span class="text-xs px-2 py-0.5 rounded-full font-semibold
        {{ match($result->status) {
          'baru'         => 'bg-blue-100 text-blue-700',
          'dicuci'       => 'bg-yellow-100 text-yellow-700',
          'disetrika'    => 'bg-orange-100 text-orange-700',
          'siap_diambil' => 'bg-green-100 text-green-700',
          'selesai'      => 'bg-gray-100 text-gray-600',
          default        => 'bg-gray-100 text-gray-600',
        } }}">{{ $result->status_label }}</span>
    </div>

    <!-- Progress steps -->
    @php
      $steps = ['baru','dicuci','disetrika','siap_diambil','selesai'];
      $labels = ['Baru','Dicuci','Disetrika','Siap Diambil','Selesai'];
      $currentIdx = array_search($result->status, $steps);
    @endphp
    <div class="flex items-center justify-between mb-3">
      @foreach($steps as $i => $step)
        <div class="flex flex-col items-center flex-1">
          <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold
            {{ $i <= $currentIdx ? 'bg-[#f39c12] text-white' : 'bg-white border-2 border-[#d8c3ad] text-[#534434]' }}">
            @if($i < $currentIdx)
              ✓
            @else
              {{ $i + 1 }}
            @endif
          </div>
          @if($i < count($steps)-1)
            <div class="h-0.5 w-full {{ $i < $currentIdx ? 'bg-[#f39c12]' : 'bg-[#d8c3ad]' }} mt-3"></div>
          @endif
        </div>
        @if($i < count($steps)-1)
        <div class="h-0.5 flex-1 {{ $i < $currentIdx ? 'bg-[#f39c12]' : 'bg-[#d8c3ad]' }} -mt-3"></div>
        @endif
      @endforeach
    </div>

    <div class="flex justify-between text-[9px] text-[#534434] px-1 mb-3">
      @foreach($labels as $label)
      <span class="text-center" style="width:{{ 100/count($labels) }}%">{{ $label }}</span>
      @endforeach
    </div>

    <div class="text-xs text-[#534434] space-y-1">
      <div class="flex justify-between">
        <span>Pelanggan:</span>
        <span class="font-medium">{{ $result->member?->name ?? 'Tamu' }}</span>
      </div>
      <div class="flex justify-between">
        <span>Total:</span>
        <span class="font-bold text-[#f39c12]">
          {{ $result->has_kiloan && $result->total == 0 ? 'TBD (Menunggu Timbang)' : 'Rp ' . number_format($result->total, 0, ',', '.') }}
        </span>
      </div>
      <div class="flex justify-between">
        <span>Pembayaran:</span>
        <span class="{{ $result->payment_status === 'lunas' ? 'text-green-600' : 'text-red-500' }} font-medium">
          {{ $result->payment_status === 'lunas' ? 'Lunas' : 'Belum Bayar' }}
        </span>
      </div>
    </div>
  </div>
  @endif
</div>