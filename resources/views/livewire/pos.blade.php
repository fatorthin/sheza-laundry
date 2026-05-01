<div x-data="{ showCartDrawer: false }" @order-processed.window="showCartDrawer = false">

    {{-- ===== MAIN GRID: Catalog + Desktop Cart Sidebar ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_20rem] min-h-[calc(100vh-8rem)]">

        {{-- LEFT: Service Catalog --}}
        <div class="flex flex-col bg-white border border-outline-variant rounded-2xl
                    lg:rounded-none lg:border-y-0 lg:border-l-0 lg:border-r
                    pb-24 lg:pb-0">

            {{-- Categories --}}
            <div class="p-4 border-b border-outline-variant">
                <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide">
                    @foreach (['semua' => 'Semua', 'kiloan' => 'Kiloan', 'satuan' => 'Satuan', 'sepatu' => 'Sepatu', 'setrika' => 'Setrika'] as $val => $label)
                        <button wire:click="$set('category', '{{ $val }}')"
                            class="shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                                   {{ $category === $val ? 'bg-primary-container text-white' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Search --}}
            <div class="p-4 border-b border-outline-variant">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]">search</span>
                    <input wire:model.live.debounce.300ms="searchService" type="text" placeholder="Cari layanan..."
                        class="w-full pl-10 pr-4 py-2.5 border border-outline-variant rounded-xl text-sm
                               focus:ring-2 focus:ring-primary-container focus:border-primary-container bg-[#fff8f4]">
                </div>
            </div>

            {{-- Services Grid --}}
            <div class="flex-1 overflow-y-auto p-4">
                <div class="grid grid-cols-2 xl:grid-cols-3 gap-3">
                    @forelse($this->services as $service)
                        <button wire:click="addService({{ $service->id }})" class="bg-white border border-outline-variant rounded-2xl p-4 text-left
                                   hover:border-primary-container hover:shadow-md transition-all active:scale-[0.97] group">
                            <div class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center mb-3
                                        group-hover:bg-primary-container/20 transition-colors">
                                <span class="material-symbols-outlined text-primary-container text-[20px] filled">{{ $service->icon }}</span>
                            </div>
                            <p class="font-semibold text-xs leading-tight">{{ $service->name }}</p>
                            <p class="text-primary-container text-xs font-semibold mt-1">
                                Rp {{ number_format($service->price, 0, ',', '.') }}/{{ $service->unit }}
                            </p>
                            @if ($service->type === 'kiloan')
                                <span class="inline-block mt-1 text-[9px] bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded-full font-medium">Kiloan</span>
                            @endif
                        </button>
                    @empty
                        <div class="col-span-3 py-12 text-center text-on-surface-variant text-sm">
                            <span class="material-symbols-outlined text-3xl mb-2 block">search_off</span>
                            Tidak ada layanan ditemukan
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT: Desktop Cart Sidebar (hidden on mobile) --}}
        <div class="hidden lg:flex flex-col bg-white border border-outline-variant
                    lg:rounded-none lg:border-y-0 lg:border-r-0 lg:border-l overflow-hidden">
            {{-- Header --}}
            <div class="p-4 border-b border-outline-variant shrink-0">
                <div class="flex items-center justify-between mb-3">
                    <span class="font-bold">Order Saat Ini</span>
                    <button wire:click="clearCart" class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus Semua</button>
                </div>
            </div>
            @include('livewire.pos.cart-body')
        </div>

    </div>{{-- END GRID --}}


    {{-- ===== MOBILE: Backdrop ===== --}}
    <div x-show="showCartDrawer" x-cloak @click="showCartDrawer = false" class="fixed inset-0 z-40 bg-black/50 lg:hidden" x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    </div>

    {{-- ===== MOBILE: Bottom Sheet Cart ===== --}}
    <div x-show="showCartDrawer" x-cloak class="fixed inset-x-0 bottom-0 z-50 flex flex-col max-h-[90vh] bg-white rounded-t-2xl shadow-2xl overflow-hidden lg:hidden" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4">

        {{-- Drag Handle --}}
        <div class="flex justify-center pt-3 pb-1 shrink-0">
            <div class="w-10 h-1 bg-outline-variant rounded-full"></div>
        </div>

        {{-- Header --}}
        <div class="px-4 pt-2 pb-3 border-b border-outline-variant shrink-0">
            <div class="flex items-center justify-between">
                <span class="font-bold">Order Saat Ini</span>
                <div class="flex items-center gap-3">
                    <button wire:click="clearCart" class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus Semua</button>
                    <button @click="showCartDrawer = false" class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined text-[22px]">keyboard_arrow_down</span>
                    </button>
                </div>
            </div>
        </div>

        @include('livewire.pos.cart-body')
    </div>

    {{-- ===== MOBILE: Floating Cart Button ===== --}}
    @if (!empty($cart))
        <div x-show="!showCartDrawer" x-cloak class="fixed bottom-4 left-4 right-4 z-30 lg:hidden">
            <button @click="showCartDrawer = true" class="w-full py-3.5 bg-primary-container text-white rounded-2xl font-bold text-sm
                       flex items-center justify-between px-5 shadow-2xl active:scale-[0.98] transition-transform">
                <div class="flex items-center gap-2.5">
                    <div class="relative">
                        <span class="material-symbols-outlined text-[20px] filled">shopping_cart</span>
                        <span class="absolute -top-2 -right-2 w-4 h-4 bg-white text-primary-container rounded-full
                                     text-[9px] font-bold flex items-center justify-center leading-none">{{ count($cart) }}</span>
                    </div>
                    <span>Lihat Pesanan</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="font-semibold">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
                    @if ($this->hasKiloan)
                        <span class="text-xs opacity-75">+</span>
                    @endif
                    <span class="material-symbols-outlined text-[20px] ml-1">expand_less</span>
                </div>
            </button>
        </div>
    @endif

    {{-- ===== Success Modal ===== --}}
    @if ($showSuccessModal)
        <div class="fixed inset-0 z-60 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-2xl p-8 max-w-sm w-full text-center shadow-2xl">
                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-green-600 text-3xl filled">check_circle</span>
                </div>
                <h3 class="text-xl font-bold mb-2">Order Berhasil!</h3>
                <p class="text-sm text-on-surface-variant mb-6">Order telah disimpan dan siap diproses.</p>
                <div class="grid grid-cols-2 gap-3">
                    @if ($lastOrderId)
                        <a href="{{ route('admin.orders') }}" class="py-3 border border-outline-variant text-sm rounded-xl text-on-surface-variant
                                   hover:bg-surface-container transition-colors text-center">
                            <span class="inline-flex items-center gap-1 justify-center">
                                <span class="material-symbols-outlined text-[16px]">view_kanban</span>
                                Manajemen Order
                            </span>
                        </a>
                    @endif
                    <button wire:click="$set('showSuccessModal', false)" class="py-3 bg-primary-container text-white text-sm rounded-xl font-semibold hover:bg-[#e08e0b]">
                        Order Baru
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
