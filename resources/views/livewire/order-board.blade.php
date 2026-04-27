<div>
  {{-- Header --}}
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-6">
    <div>
      <h1 class="text-xl font-bold">Manajemen Order</h1>
      <p class="text-sm text-[#534434]">Pantau dan kelola status pesanan laundry</p>
    </div>
    <div class="flex gap-2">
      <div class="relative">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#534434] text-[18px]">search</span>
        <input wire:model.live.debounce.400ms="search" type="text" placeholder="Cari order..."
               class="pl-10 pr-4 py-2 border border-[#d8c3ad] rounded-xl text-sm focus:ring-2 focus:ring-[#f39c12] focus:outline-none bg-white w-48">
      </div>
      <select wire:model.live="filterPayment" class="border border-[#d8c3ad] rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#f39c12] focus:outline-none bg-white">
        <option value="">Semua Pembayaran</option>
        <option value="belum_bayar">Belum Bayar</option>
        <option value="lunas">Lunas</option>
      </select>
      <a href="{{ route('admin.pos') }}" class="flex items-center gap-1 px-4 py-2 bg-[#f39c12] text-white rounded-xl text-sm font-medium hover:bg-[#e08e0b]">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Order Baru
      </a>
    </div>
  </div>

  {{-- Kanban Board --}}
  <div class="flex gap-4 overflow-x-auto pb-4">
    @php
      $statusConfig = [
        'baru'         => ['label'=>'Baru',         'color'=>'blue',   'icon'=>'fiber_new'],
        'dicuci'       => ['label'=>'Dicuci',        'color'=>'yellow', 'icon'=>'local_laundry_service'],
        'disetrika'    => ['label'=>'Disetrika',     'color'=>'orange', 'icon'=>'iron'],
        'siap_diambil' => ['label'=>'Siap Diambil',  'color'=>'green',  'icon'=>'done_all'],
        'selesai'      => ['label'=>'Selesai',       'color'=>'gray',   'icon'=>'check_circle'],
      ];
      $colorMap = [
        'blue'   => ['col'=>'bg-blue-50 border-blue-100',   'badge'=>'bg-blue-100 text-blue-700',   'icon'=>'text-blue-500'],
        'yellow' => ['col'=>'bg-yellow-50 border-yellow-100','badge'=>'bg-yellow-100 text-yellow-700','icon'=>'text-yellow-500'],
        'orange' => ['col'=>'bg-orange-50 border-orange-100','badge'=>'bg-orange-100 text-orange-700','icon'=>'text-orange-500'],
        'green'  => ['col'=>'bg-green-50 border-green-100', 'badge'=>'bg-green-100 text-green-700', 'icon'=>'text-green-600'],
        'gray'   => ['col'=>'bg-gray-50 border-gray-200',   'badge'=>'bg-gray-100 text-gray-600',   'icon'=>'text-gray-400'],
      ];
    @endphp

    @foreach($statusConfig as $status => $cfg)
    @php
      $orders = $this->orders->get($status, collect());
      $c = $colorMap[$cfg['color']];
    @endphp
    <div class="flex-shrink-0 w-72 flex flex-col">
      {{-- Column header --}}
      <div class="flex items-center gap-2 mb-3">
        <span class="material-symbols-outlined {{ $c['icon'] }} text-[18px] filled">{{ $cfg['icon'] }}</span>
        <span class="font-semibold text-sm">{{ $cfg['label'] }}</span>
        <span class="{{ $c['badge'] }} text-xs font-bold px-2 py-0.5 rounded-full ml-auto">{{ $orders->count() }}</span>
      </div>

      {{-- Cards --}}
      <div class="flex-1 {{ $c['col'] }} rounded-2xl border p-3 space-y-3 min-h-32">
        @forelse($orders as $order)
        <div class="bg-white rounded-xl p-3 shadow-sm border border-[#d8c3ad] group hover:shadow-md transition-shadow"
             x-data="{ open: false }">
          {{-- Card header --}}
          <div class="flex items-start justify-between mb-2">
            <div>
              <p class="text-xs font-bold text-[#865300]">{{ $order->order_number }}</p>
              <p class="text-[10px] text-[#534434]">{{ $order->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex items-center gap-1">
              @if($order->is_express)
              <span class="text-[9px] bg-[#f39c12] text-white px-1.5 py-0.5 rounded-full font-bold">EXPRESS</span>
              @endif
              <span class="text-[9px] px-1.5 py-0.5 rounded-full font-bold
                {{ $order->payment_status === 'lunas' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                {{ $order->payment_status === 'lunas' ? '✓ Lunas' : '✗ Belum Bayar' }}
              </span>
            </div>
          </div>

          <p class="font-semibold text-sm">{{ $order->member?->name ?? 'Tamu' }}</p>
          <p class="text-xs text-[#534434] mt-0.5">
            {{ $order->items->count() }} item{{ $order->has_kiloan ? ' · Menunggu Timbang' : '' }}
          </p>

          <div class="flex items-center justify-between mt-2.5 pt-2.5 border-t border-[#f0e0d2]">
            <span class="text-sm font-bold text-[#f39c12]">
              {{ $order->total == 0 ? 'TBD' : 'Rp ' . number_format($order->total, 0, ',', '.') }}
            </span>

            <div class="flex items-center gap-1">
              {{-- Actions dropdown --}}
              <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.outside="open = false"
                        class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-[#fbebdd] text-[#534434] transition-colors">
                  <span class="material-symbols-outlined text-[18px]">more_vert</span>
                </button>
                <div x-show="open" x-cloak
                     class="absolute right-0 top-full mt-1 w-48 bg-white rounded-xl shadow-xl border border-[#d8c3ad] z-10 overflow-hidden">
                  @if($order->payment_status === 'belum_bayar')
                  <button wire:click="markPaid({{ $order->id }})" @click="open=false"
                          class="w-full px-3 py-2.5 text-xs text-left hover:bg-[#fbebdd] flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined text-[16px] text-green-600">payments</span>
                    Tandai Lunas
                  </button>
                  @endif
                  <a href="{{ route('admin.orders.show', $order) }}" @click="open=false"
                     class="w-full px-3 py-2.5 text-xs text-left hover:bg-[#fbebdd] flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined text-[16px] text-[#534434]">visibility</span>
                    Detail Order
                  </a>
                  <a href="{{ route('admin.receipt', $order) }}" target="_blank" @click="open=false"
                     class="w-full px-3 py-2.5 text-xs text-left hover:bg-[#fbebdd] flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined text-[16px] text-[#534434]">print</span>
                    Cetak Struk
                  </a>
                  @if($order->member)
                  <a href="https://wa.me/62{{ ltrim($order->member->phone, '0') }}?text=Halo {{ urlencode($order->member->name) }}, order Anda ({{ $order->order_number }}) sudah {{ $order->status_label }}."
                     target="_blank" @click="open=false"
                     class="w-full px-3 py-2.5 text-xs text-left hover:bg-[#fbebdd] flex items-center gap-2 transition-colors border-t border-[#f0e0d2]">
                    <span class="material-symbols-outlined text-[16px] text-green-600">chat</span>
                    Kirim WA
                  </a>
                  @endif
                </div>
              </div>

              {{-- Quick status advance --}}
              @if($status !== 'selesai')
              @php
                $nextStatus = match($status) {
                  'baru' => 'dicuci', 'dicuci' => 'disetrika',
                  'disetrika' => 'siap_diambil', 'siap_diambil' => 'selesai', default => null
                };
                $nextLabel = match($status) {
                  'baru' => 'Mulai Cuci', 'dicuci' => 'Setrika',
                  'disetrika' => 'Siap Ambil', 'siap_diambil' => 'Selesai', default => null
                };
              @endphp
              @if($nextStatus)
              <button wire:click="updateStatus({{ $order->id }}, '{{ $nextStatus }}')"
                      class="px-2.5 py-1 bg-[#f39c12] hover:bg-[#e08e0b] text-white rounded-lg text-[10px] font-bold transition-colors flex items-center gap-1">
                {{ $nextLabel }} →
              </button>
              @endif
              @endif
            </div>
          </div>
        </div>
        @empty
        <div class="py-8 text-center text-xs text-[#534434] opacity-60">
          <span class="material-symbols-outlined text-2xl block mb-1">inbox</span>
          Tidak ada order
        </div>
        @endforelse
      </div>
    </div>
    @endforeach
  </div>
</div>