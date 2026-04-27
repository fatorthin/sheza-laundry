<div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_20rem] min-h-[calc(100vh-8rem)] gap-4 lg:gap-0" x-data>
    {{-- Left: Service Catalog --}}
    <div
        class="flex flex-col min-w-0 bg-white rounded-2xl border border-[#d8c3ad] lg:rounded-none lg:border-y-0 lg:border-l-0 lg:border-r">
        {{-- Categories --}}
        <div class="p-4 border-b border-[#d8c3ad] bg-white">
            <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide">
                @foreach (['semua' => 'Semua', 'kiloan' => 'Kiloan', 'satuan' => 'Satuan', 'sepatu' => 'Sepatu', 'setrika' => 'Setrika'] as $val => $label)
                    <button wire:click="$set('category', '{{ $val }}')"
                        class="flex-shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                       {{ $category === $val ? 'bg-[#f39c12] text-white' : 'bg-[#fbebdd] text-[#534434] hover:bg-[#f5e5d7]' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Search --}}
        <div class="p-4 border-b border-[#d8c3ad] bg-white">
            <div class="relative">
                <span
                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#534434] text-[18px]">search</span>
                <input wire:model.live.debounce.300ms="searchService" type="text" placeholder="Cari layanan..."
                    class="w-full pl-10 pr-4 py-2.5 border border-[#d8c3ad] rounded-xl text-sm focus:ring-2 focus:ring-[#f39c12] focus:border-[#f39c12] bg-[#fff8f4]">
            </div>
        </div>

        {{-- Services Grid --}}
        <div class="flex-1 overflow-y-auto p-4">
            <div class="grid grid-cols-2 xl:grid-cols-3 gap-3">
                @forelse($this->services as $service)
                    <button wire:click="addService({{ $service->id }})"
                        class="bg-white border border-[#d8c3ad] rounded-2xl p-4 text-left hover:border-[#f39c12] hover:shadow-md transition-all active:scale-[0.97] group">
                        <div
                            class="w-10 h-10 rounded-xl bg-[#fbebdd] flex items-center justify-center mb-3 group-hover:bg-[#f39c12]/20 transition-colors">
                            <span
                                class="material-symbols-outlined text-[#f39c12] text-[20px] filled">{{ $service->icon }}</span>
                        </div>
                        <p class="font-semibold text-xs leading-tight">{{ $service->name }}</p>
                        <p class="text-[#f39c12] text-xs font-semibold mt-1">
                            Rp {{ number_format($service->price, 0, ',', '.') }}/{{ $service->unit }}
                        </p>
                        @if ($service->type === 'kiloan')
                            <span
                                class="inline-block mt-1 text-[9px] bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded-full font-medium">Kiloan</span>
                        @endif
                    </button>
                @empty
                    <div class="col-span-3 py-12 text-center text-[#534434] text-sm">
                        <span class="material-symbols-outlined text-3xl mb-2 block">search_off</span>
                        Tidak ada layanan ditemukan
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right: Cart --}}
    <div
        class="flex flex-col bg-white rounded-2xl border border-[#d8c3ad] overflow-hidden lg:rounded-none lg:border-y-0 lg:border-r-0 lg:border-l">
        {{-- Header --}}
        <div class="p-4 border-b border-[#d8c3ad]">
            <div class="flex items-center justify-between mb-3">
                <span class="font-bold">Order Saat Ini</span>
                <button wire:click="clearCart" class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus
                    Semua</button>
            </div>

            {{-- Customer selector --}}
            <div class="relative">
                @if ($selectedMemberId && $this->selectedMember)
                    <div class="flex items-center gap-2 px-3 py-2 bg-[#fbebdd] border border-[#d8c3ad] rounded-xl">
                        <div
                            class="w-7 h-7 rounded-full bg-[#f39c12] flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($this->selectedMember->name, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold truncate">{{ $this->selectedMember->name }}</p>
                            <p class="text-[10px] text-[#534434]">{{ $this->selectedMember->phone }}</p>
                        </div>
                        <button wire:click="clearMember" class="text-[#534434] hover:text-red-500">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                        </button>
                    </div>
                @else
                    <button wire:click="$set('showMemberSearch', true)"
                        class="w-full flex items-center gap-2 px-3 py-2.5 border border-dashed border-[#d8c3ad] rounded-xl text-sm text-[#534434] hover:border-[#f39c12] hover:text-[#865300] transition-colors">
                        <span class="material-symbols-outlined text-[18px]">person_add</span>
                        Pilih Pelanggan
                        <span class="ml-auto material-symbols-outlined text-[16px]">chevron_right</span>
                    </button>
                @endif

                @if ($showMemberSearch)
                    <div
                        class="absolute top-full left-0 right-0 z-50 mt-1 bg-white border border-[#d8c3ad] rounded-xl shadow-xl">
                        <div class="p-2">
                            <input wire:model.live.debounce.300ms="searchMember" type="text"
                                placeholder="Cari nama / HP..."
                                class="w-full px-3 py-2 text-sm border border-[#d8c3ad] rounded-lg focus:ring-2 focus:ring-[#f39c12] focus:outline-none"
                                autofocus>
                        </div>
                        @foreach ($this->members as $m)
                            <button wire:click="selectMember({{ $m->id }})"
                                class="w-full flex items-center gap-2 px-3 py-2 hover:bg-[#fbebdd] text-left transition-colors">
                                <div
                                    class="w-7 h-7 rounded-full bg-[#f39c12] flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($m->name, 0, 2)) }}</div>
                                <div>
                                    <p class="text-xs font-medium">{{ $m->name }}</p>
                                    <p class="text-[10px] text-[#534434]">{{ $m->phone }}</p>
                                </div>
                            </button>
                        @endforeach
                        <button wire:click="$set('showMemberSearch', false)"
                            class="w-full px-3 py-2 text-xs text-[#534434] hover:bg-gray-50 border-t border-[#d8c3ad]">Tutup</button>
                    </div>
                @endif
            </div>
        </div>

        {{-- Cart items --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-2 max-h-[45vh] lg:max-h-none">
            @if (empty($cart))
                <div class="py-12 text-center text-[#534434]">
                    <span class="material-symbols-outlined text-4xl text-[#d8c3ad] block mb-2">shopping_cart</span>
                    <p class="text-sm">Keranjang kosong</p>
                    <p class="text-xs mt-1">Pilih layanan untuk mulai</p>
                </div>
            @else
                @foreach ($cart as $key => $item)
                    <div class="bg-[#fbebdd] rounded-xl p-3">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold truncate">{{ $item['name'] }}</p>
                                @if ($item['type'] === 'kiloan')
                                    <div class="flex items-center gap-1 mt-0.5">
                                        <span
                                            class="material-symbols-outlined text-orange-500 text-[12px]">warning</span>
                                        <span class="text-[10px] text-orange-600 font-medium">Menunggu Timbang</span>
                                    </div>
                                @else
                                    <p class="text-[10px] text-[#534434]">Rp
                                        {{ number_format($item['price'], 0, ',', '.') }}/{{ $item['unit'] }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                @if ($item['type'] === 'kiloan')
                                    <span class="text-xs font-bold text-orange-500">TBD</span>
                                @else
                                    <span class="text-xs font-bold">Rp
                                        {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                @endif
                                <button wire:click="removeItem('{{ $key }}')"
                                    class="ml-2 text-red-400 hover:text-red-600">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                            </div>
                        </div>
                        @if ($item['type'] === 'satuan')
                            <div class="flex items-center gap-2 mt-2">
                                <button wire:click="decrementQuantity('{{ $key }}')"
                                    class="w-7 h-7 rounded-lg border border-[#d8c3ad] bg-white flex items-center justify-center text-[#534434] hover:border-[#f39c12] hover:text-[#f39c12] transition-colors">−</button>
                                <span class="text-sm font-bold w-6 text-center">{{ intval($item['quantity']) }}</span>
                                <button wire:click="incrementQuantity('{{ $key }}')"
                                    class="w-7 h-7 rounded-lg border border-[#d8c3ad] bg-white flex items-center justify-center text-[#534434] hover:border-[#f39c12] hover:text-[#f39c12] transition-colors">+</button>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Totals & Payment --}}
        <div class="p-4 border-t border-[#d8c3ad] bg-[#fbebdd] space-y-3">
            <div class="space-y-1.5 text-sm">
                <div class="flex justify-between text-[#534434]"><span>Subtotal</span><span>Rp
                        {{ number_format($this->subtotal, 0, ',', '.') }}</span></div>
                <div class="flex justify-between text-[#534434]"><span>PPN (11%)</span><span>Rp
                        {{ number_format($this->tax, 0, ',', '.') }}</span></div>
                <div class="flex justify-between font-bold text-base pt-1 border-t border-[#d8c3ad]">
                    <span>Total @if ($this->hasKiloan)
                            (Est)
                        @endif
                    </span>
                    <span class="text-[#f39c12]">
                        Rp {{ number_format($this->total, 0, ',', '.') }}@if ($this->hasKiloan)
                            +
                        @endif
                    </span>
                </div>
            </div>

            @if ($this->hasKiloan)
                <div class="flex items-start gap-2 p-2 bg-orange-50 border border-orange-200 rounded-xl">
                    <span class="material-symbols-outlined text-orange-500 text-[16px] mt-0.5">info</span>
                    <p class="text-[10px] text-orange-700 leading-relaxed">Total & pembayaran akan difinalisasi setelah
                        berat aktual ditimbang.</p>
                </div>
            @endif

            {{-- Payment Method --}}
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wide text-[#534434] mb-1.5">Metode Bayar</p>
                <div class="grid grid-cols-3 gap-1.5">
                    @foreach (['tunai' => 'Tunai', 'qris' => 'QRIS', 'transfer' => 'Transfer'] as $val => $label)
                        <button wire:click="$set('paymentMethod', '{{ $val }}')"
                            class="py-1.5 text-xs font-semibold rounded-lg transition-colors
                         {{ $paymentMethod === $val ? 'bg-[#f39c12] text-white' : 'bg-white border border-[#d8c3ad] text-[#534434] hover:border-[#f39c12]' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <button wire:click="processOrder" wire:loading.attr="disabled"
                class="w-full py-3.5 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all active:scale-[0.98]
                     {{ empty($cart) ? 'bg-[#d8c3ad] text-white cursor-not-allowed' : 'bg-[#f39c12] hover:bg-[#e08e0b] text-white shadow-md' }}">
                <span wire:loading.remove class="material-symbols-outlined text-[18px]">point_of_sale</span>
                <span wire:loading class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="white" stroke-width="3"
                            opacity="0.3" />
                        <path d="M12 2a10 10 0 0110 10" stroke="white" stroke-width="3" stroke-linecap="round" />
                    </svg>
                </span>
                {{ $this->hasKiloan ? 'Buat Order Baru' : 'Proses Order & Cetak' }}
            </button>
        </div>
    </div>

    {{-- Success Modal --}}
    @if ($showSuccessModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-2xl p-8 max-w-sm w-full text-center shadow-2xl">
                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-green-600 text-3xl filled">check_circle</span>
                </div>
                <h3 class="text-xl font-bold mb-2">Order Berhasil!</h3>
                <p class="text-sm text-[#534434] mb-6">Order telah disimpan dan siap diproses.</p>
                <div class="grid grid-cols-2 gap-3">
                    @if ($lastOrderId)
                        <a href="{{ route('admin.receipt', $lastOrderId) }}"
                            class="py-3 border border-[#d8c3ad] text-sm rounded-xl text-[#534434] hover:bg-[#fbebdd] transition-colors text-center">
                            🖨️ Cetak Struk
                        </a>
                    @endif
                    <button wire:click="$set('showSuccessModal', false)"
                        class="py-3 bg-[#f39c12] text-white text-sm rounded-xl font-semibold hover:bg-[#e08e0b]">
                        Order Baru
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
