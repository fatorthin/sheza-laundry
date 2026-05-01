{{--
  Shared cart body partial — included in both desktop sidebar and mobile bottom sheet.
  Requires Livewire POS component context ($cart, $this->*, etc.)
--}}

{{-- Customer selector --}}
<div class="p-4 border-b border-outline-variant shrink-0 space-y-2">

    {{-- [STATE: member selected] --}}
    @if ($selectedMemberId && $this->selectedMember)
        <div class="flex items-center gap-2 px-3 py-2 bg-green-50 border border-green-200 rounded-xl">
            <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center text-white text-xs font-bold shrink-0">
                {{ strtoupper(substr($this->selectedMember->name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold truncate">{{ $this->selectedMember->name }}</p>
                <p class="text-[10px] text-on-surface-variant">{{ $this->selectedMember->phone }}</p>
            </div>
            <button wire:click="clearMember" class="text-on-surface-variant hover:text-red-500 shrink-0">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        {{-- [STATE: no panel open] → show two-option trigger --}}
    @elseif ($memberPanel === '')
        <div class="grid grid-cols-2 gap-2">
            <button wire:click="openMemberPanel('search')" class="flex items-center justify-center gap-1.5 px-3 py-2.5 border border-dashed border-outline-variant rounded-xl text-xs text-on-surface-variant hover:border-primary-container hover:text-[#865300] transition-colors">
                <span class="material-symbols-outlined text-[16px]">manage_search</span>
                Cari Pelanggan
            </button>
            <button wire:click="openMemberPanel('new')" class="flex items-center justify-center gap-1.5 px-3 py-2.5 border border-dashed border-primary-container rounded-xl text-xs text-primary-container hover:bg-primary-container/10 transition-colors font-medium">
                <span class="material-symbols-outlined text-[16px]">person_add</span>
                Pelanggan Baru
            </button>
        </div>

        {{-- [STATE: search panel] --}}
    @elseif ($memberPanel === 'search')
        <div class="space-y-1">
            <div class="flex items-center gap-2 mb-1.5">
                <span class="text-xs font-semibold text-on-surface flex-1">Cari Pelanggan</span>
                <button wire:click="closeMemberPanel" class="text-[10px] text-on-surface-variant hover:text-red-500 flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-[14px]">close</span> Tutup
                </button>
            </div>
            <input wire:model.live.debounce.300ms="searchMember" type="text" placeholder="Nama atau No. HP..." class="w-full px-3 py-2 text-sm border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary-container focus:outline-none bg-[#fff8f4]" autofocus>
            @if (strlen($searchMember) >= 2)
                <div class="border border-outline-variant rounded-xl overflow-hidden mt-1">
                    @forelse ($this->members as $m)
                        <button wire:click="selectMember({{ $m->id }})" class="w-full flex items-center gap-2 px-3 py-2 hover:bg-surface-container text-left transition-colors border-b border-outline-variant last:border-0">
                            <div class="w-7 h-7 rounded-full bg-primary-container flex items-center justify-center text-white text-[11px] font-bold shrink-0">
                                {{ strtoupper(substr($m->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-medium truncate">{{ $m->name }}</p>
                                <p class="text-[10px] text-on-surface-variant">{{ $m->phone }}</p>
                            </div>
                        </button>
                    @empty
                        <div class="px-3 py-3 text-xs text-on-surface-variant text-center">
                            Pelanggan tidak ditemukan.
                            <button wire:click="openMemberPanel('new')" class="text-primary-container font-semibold ml-1 hover:underline">Daftarkan?</button>
                        </div>
                    @endforelse
                </div>
            @endif
        </div>

        {{-- [STATE: new member form] --}}
    @elseif ($memberPanel === 'new')
        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="material-symbols-outlined text-primary-container text-[18px]">person_add</span>
                <span class="text-xs font-semibold text-on-surface flex-1">Daftarkan Pelanggan Baru</span>
                <button wire:click="closeMemberPanel" class="text-[10px] text-on-surface-variant hover:text-red-500 flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-[14px]">close</span> Batal
                </button>
            </div>

            <div class="space-y-2">
                {{-- Name --}}
                <div>
                    <input wire:model="newMemberName" type="text" placeholder="Nama lengkap *"
                        class="w-full px-3 py-2.5 text-sm border rounded-xl focus:ring-2 focus:ring-primary-container focus:outline-none bg-[#fff8f4]
                               {{ $errors->has('newMemberName') ? 'border-red-400 bg-red-50' : 'border-outline-variant' }}">
                    @error('newMemberName')
                        <p class="text-[10px] text-red-500 mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <input wire:model="newMemberPhone" type="tel" placeholder="No. HP / WhatsApp *"
                        class="w-full px-3 py-2.5 text-sm border rounded-xl focus:ring-2 focus:ring-primary-container focus:outline-none bg-[#fff8f4]
                               {{ $errors->has('newMemberPhone') ? 'border-red-400 bg-red-50' : 'border-outline-variant' }}">
                    @error('newMemberPhone')
                        <p class="text-[10px] text-red-500 mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Address (optional) --}}
                <input wire:model="newMemberAddress" type="text" placeholder="Alamat (opsional)" class="w-full px-3 py-2.5 text-sm border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary-container focus:outline-none bg-[#fff8f4]">

                {{-- Submit --}}
                <button wire:click="createMember" wire:loading.attr="disabled" class="w-full py-2.5 bg-primary-container text-white rounded-xl text-sm font-semibold hover:bg-[#e08e0b] transition-colors flex items-center justify-center gap-2 active:scale-[0.98]">
                    <span wire:loading.remove class="material-symbols-outlined text-[16px]">how_to_reg</span>
                    <span wire:loading>
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="white" stroke-width="3" opacity="0.3" />
                            <path d="M12 2a10 10 0 0110 10" stroke="white" stroke-width="3" stroke-linecap="round" />
                        </svg>
                    </span>
                    <span wire:loading.remove>Simpan & Pilih</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>
        </div>
    @endif

</div>

{{-- Cart items --}}
<div class="flex-1 overflow-y-auto p-4 space-y-2">
    @if (empty($cart))
        <div class="py-12 text-center text-on-surface-variant">
            <span class="material-symbols-outlined text-4xl text-outline-variant block mb-2">shopping_cart</span>
            <p class="text-sm">Keranjang kosong</p>
            <p class="text-xs mt-1">Pilih layanan untuk mulai</p>
        </div>
    @else
        @foreach ($cart as $key => $item)
            <div class="bg-surface-container rounded-xl p-3">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold truncate">{{ $item['name'] }}</p>
                        @if ($item['type'] === 'kiloan')
                            <div class="flex items-center gap-1 mt-0.5">
                                <span class="material-symbols-outlined text-orange-500 text-[12px]">warning</span>
                                <span class="text-[10px] text-orange-600 font-medium">Menunggu Timbang</span>
                            </div>
                        @else
                            <p class="text-[10px] text-on-surface-variant">Rp
                                {{ number_format($item['price'], 0, ',', '.') }}/{{ $item['unit'] }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-1">
                        @if ($item['type'] === 'kiloan')
                            <span class="text-xs font-bold text-orange-500">TBD</span>
                        @else
                            <span class="text-xs font-bold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                        @endif
                        <button wire:click="removeItem('{{ $key }}')" class="ml-1 text-red-400 hover:text-red-600">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                        </button>
                    </div>
                </div>
                @if ($item['type'] === 'satuan')
                    <div class="flex items-center gap-2 mt-2">
                        <button wire:click="decrementQuantity('{{ $key }}')" class="w-7 h-7 rounded-lg border border-outline-variant bg-white flex items-center justify-center text-on-surface-variant hover:border-primary-container hover:text-primary-container transition-colors">−</button>
                        <span class="text-sm font-bold w-6 text-center">{{ intval($item['quantity']) }}</span>
                        <button wire:click="incrementQuantity('{{ $key }}')" class="w-7 h-7 rounded-lg border border-outline-variant bg-white flex items-center justify-center text-on-surface-variant hover:border-primary-container hover:text-primary-container transition-colors">+</button>
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</div>

{{-- Totals & Process button --}}
<div class="p-4 border-t border-outline-variant bg-surface-container space-y-3 shrink-0">
    <div class="space-y-1.5 text-sm">
        <div class="flex justify-between text-on-surface-variant">
            <span>Subtotal</span>
            <span>Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between font-bold text-base pt-1 border-t border-outline-variant">
            <span>Total @if ($this->hasKiloan)
                    (Est)
                @endif
            </span>
            <span class="text-primary-container">
                Rp {{ number_format($this->total, 0, ',', '.') }}@if ($this->hasKiloan)
                    +
                @endif
            </span>
        </div>
    </div>

    @if ($this->hasKiloan)
        <div class="flex items-start gap-2 p-2 bg-orange-50 border border-orange-200 rounded-xl">
            <span class="material-symbols-outlined text-orange-500 text-[16px] mt-0.5">info</span>
            <p class="text-[10px] text-orange-700 leading-relaxed">Total & pembayaran akan difinalisasi setelah berat
                aktual ditimbang.</p>
        </div>
    @endif

    <button wire:click="processOrder" wire:loading.attr="disabled"
        class="w-full py-3.5 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all active:scale-[0.98]
             {{ empty($cart) ? 'bg-outline-variant text-white cursor-not-allowed' : 'bg-primary-container hover:bg-[#e08e0b] text-white shadow-md' }}">
        <span wire:loading.remove class="material-symbols-outlined text-[18px]">point_of_sale</span>
        <span wire:loading class="flex items-center gap-2">
            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="white" stroke-width="3" opacity="0.3" />
                <path d="M12 2a10 10 0 0110 10" stroke="white" stroke-width="3" stroke-linecap="round" />
            </svg>
        </span>
        {{ $this->hasKiloan ? 'Buat Order Baru' : 'Proses Order & Cetak' }}
    </button>
</div>
