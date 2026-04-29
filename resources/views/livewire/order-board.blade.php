<div>
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl font-bold">Manajemen Order</h1>
            <p class="text-sm text-on-surface-variant">Pantau dan kelola status pesanan laundrymu</p>
        </div>
        <div class="flex flex-col lg:flex-row lg:items-center gap-2">
            <div class="relative w-full lg:w-48">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="material-symbols-outlined text-on-surface-variant text-[18px]">search</span>
                </div>
                <input wire:model.live.debounce.400ms="search" type="text" placeholder="Cari order..."
                    class="h-10 pl-10 pr-4 border border-outline-variant rounded-xl text-sm focus:ring-2 focus:ring-primary-container focus:outline-none bg-white w-full">
            </div>
            <select wire:model.live="filterPayment"
                class="h-10 border border-outline-variant rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary-container focus:outline-none bg-white w-full lg:w-40">
                <option value="">Semua Pembayaran</option>
                <option value="belum_bayar">Belum Bayar</option>
                <option value="lunas">Lunas</option>
            </select>
            <select wire:model.live="filterStatus"
                class="h-10 border border-outline-variant rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary-container focus:outline-none bg-white w-full lg:w-36">
                <option value="">Semua Status Order</option>
                @foreach ($statuses as $statusVal => $statusLabel)
                    <option value="{{ $statusVal }}">{{ $statusLabel }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterMember"
                class="h-10 border border-outline-variant rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary-container focus:outline-none bg-white w-full lg:w-40">
                <option value="">Semua Pelanggan Laundry</option>
                <option value="guest">Tamu</option>
                @foreach ($this->membersForFilter as $member)
                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                @endforeach
            </select>
            <a href="{{ route('admin.pos') }}"
                class="h-10 w-full lg:w-auto flex items-center justify-center gap-1 px-4 py-2 bg-primary-container text-white rounded-xl text-sm font-medium hover:bg-[#e08e0b] whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>Order Baru</span>
            </a>
        </div>
    </div>

    {{-- Kanban Board --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 pb-4">
        @php
            $statusConfig = [
                'baru' => ['label' => 'Baru', 'color' => 'blue', 'icon' => 'fiber_new'],
                'dicuci' => ['label' => 'Dicuci', 'color' => 'yellow', 'icon' => 'local_laundry_service'],
                'disetrika' => ['label' => 'Disetrika', 'color' => 'orange', 'icon' => 'iron'],
                'siap_diambil' => ['label' => 'Siap Diambil', 'color' => 'green', 'icon' => 'done_all'],
                'selesai' => ['label' => 'Selesai', 'color' => 'gray', 'icon' => 'check_circle'],
            ];
            $colorMap = [
                'blue' => [
                    'col' => 'bg-blue-50 border-blue-100',
                    'badge' => 'bg-blue-100 text-blue-700',
                    'icon' => 'text-blue-500',
                ],
                'yellow' => [
                    'col' => 'bg-yellow-50 border-yellow-100',
                    'badge' => 'bg-yellow-100 text-yellow-700',
                    'icon' => 'text-yellow-500',
                ],
                'orange' => [
                    'col' => 'bg-orange-50 border-orange-100',
                    'badge' => 'bg-orange-100 text-orange-700',
                    'icon' => 'text-orange-500',
                ],
                'green' => [
                    'col' => 'bg-green-50 border-green-100',
                    'badge' => 'bg-green-100 text-green-700',
                    'icon' => 'text-green-600',
                ],
                'gray' => [
                    'col' => 'bg-gray-50 border-gray-200',
                    'badge' => 'bg-gray-100 text-gray-600',
                    'icon' => 'text-gray-400',
                ],
            ];
        @endphp

        @foreach ($statusConfig as $status => $cfg)
            @php
                $orders = $this->orders->get($status, collect());
                $c = $colorMap[$cfg['color']];
                $colLimit = 5;
                $hiddenCount = max(0, $orders->count() - $colLimit);
            @endphp
            <div class="min-w-0 flex flex-col" x-data="{ showAll: false }">
                {{-- Column header --}}
                <div class="flex items-center gap-2 mb-3">
                    <span
                        class="material-symbols-outlined {{ $c['icon'] }} text-[18px] filled">{{ $cfg['icon'] }}</span>
                    <span class="font-semibold text-sm">{{ $cfg['label'] }}</span>
                    <span
                        class="{{ $c['badge'] }} text-xs font-bold px-2 py-0.5 rounded-full ml-auto">{{ $orders->count() }}</span>
                </div>

                {{-- Cards --}}
                <div class="flex-1 {{ $c['col'] }} rounded-2xl border p-3 space-y-3 min-h-32">
                    @forelse($orders as $order)
                        <div wire:key="order-{{ $order->id }}-{{ $status }}"
                            @if ($loop->index >= $colLimit) x-show="showAll" x-cloak @endif
                            class="bg-white rounded-xl p-3 shadow-sm border border-outline-variant group hover:shadow-md transition-shadow"
                            x-data="{ open: false }">
                            {{-- Card header --}}
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <p class="text-xs font-bold text-[#865300]">{{ $order->order_number }}</p>
                                    <p class="text-[10px] text-on-surface-variant">
                                        {{ $order->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="flex items-center gap-1">
                                    @if ($order->is_express)
                                        <span
                                            class="text-[9px] bg-primary-container text-white px-1.5 py-0.5 rounded-full font-bold">EXPRESS</span>
                                    @endif
                                    <span
                                        class="text-[9px] px-1.5 py-0.5 rounded-full font-bold
                {{ $order->payment_status === 'lunas' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                        {{ $order->payment_status === 'lunas' ? '✓ Lunas' : '✗ Belum Bayar' }}
                                    </span>
                                    @if ($order->payment_status === 'lunas' && $order->payment_method)
                                        <span
                                            class="text-[9px] px-1.5 py-0.5 rounded-full font-bold bg-blue-100 text-blue-700">
                                            {{ strtoupper($order->payment_method) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @php
                                $kiloanItem = $order->items->firstWhere('service_type', 'kiloan');
                                $isWaitingWeight =
                                    $order->has_kiloan && (!$kiloanItem || (float) ($kiloanItem->weight ?? 0) <= 0);
                            @endphp
                            <p class="font-semibold text-sm">{{ $order->member?->name ?? 'Tamu' }}</p>
                            <p class="text-xs text-on-surface-variant mt-0.5">
                                {{ $order->items->count() }} item{{ $isWaitingWeight ? ' · Menunggu Timbang' : '' }}
                            </p>

                            <div class="flex items-center justify-between mt-2.5 pt-2.5 border-t border-[#f0e0d2]">
                                <span class="text-sm font-bold text-primary-container">
                                    {{ $order->total == 0 ? 'TBD' : 'Rp ' . number_format($order->total, 0, ',', '.') }}
                                </span>

                                <div class="flex items-center gap-1">
                                    {{-- Actions dropdown --}}
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" @click.outside="open = false"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-surface-container text-on-surface-variant transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">more_vert</span>
                                        </button>
                                        <div x-show="open" x-cloak
                                            class="absolute right-0 top-full mt-1 w-48 bg-white rounded-xl shadow-xl border border-outline-variant z-10 overflow-hidden">
                                            @if ($order->has_kiloan && $status === 'siap_diambil')
                                                <button
                                                    @click="open=false; $dispatch('open-weigh-modal', { orderId: {{ $order->id }} })"
                                                    class="w-full px-3 py-2.5 text-xs text-left hover:bg-surface-container flex items-center gap-2 transition-colors">
                                                    <span
                                                        class="material-symbols-outlined text-[16px] text-blue-600">weight</span>
                                                    Update Berat
                                                </button>
                                            @endif
                                            @if ($order->payment_status === 'belum_bayar' && $status === 'siap_diambil')
                                                <button
                                                    @click="open=false; $dispatch('open-payment-modal', { orderId: {{ $order->id }}, orderNumber: '{{ $order->order_number }}' })"
                                                    class="w-full px-3 py-2.5 text-xs text-left hover:bg-surface-container flex items-center gap-2 transition-colors">
                                                    <span
                                                        class="material-symbols-outlined text-[16px] text-green-600">payments</span>
                                                    Tandai Lunas
                                                </button>
                                            @endif
                                            <button
                                                @click="open=false; if (confirm('Hapus pesanan ini?')) { $wire.deleteOrder({{ $order->id }}) }"
                                                class="w-full px-3 py-2.5 text-xs text-left hover:bg-surface-container flex items-center gap-2 transition-colors text-red-600 border-t border-[#f0e0d2]">
                                                <span class="material-symbols-outlined text-[16px]">delete</span>
                                                Hapus Pesanan
                                            </button>
                                            <a href="{{ route('admin.orders.show', $order) }}" @click="open=false"
                                                class="w-full px-3 py-2.5 text-xs text-left hover:bg-surface-container flex items-center gap-2 transition-colors">
                                                <span
                                                    class="material-symbols-outlined text-[16px] text-on-surface-variant">visibility</span>
                                                Detail Order
                                            </a>
                                            <a href="{{ route('admin.receipt', $order) }}" target="_blank"
                                                @click="open=false"
                                                class="w-full px-3 py-2.5 text-xs text-left hover:bg-surface-container flex items-center gap-2 transition-colors">
                                                <span
                                                    class="material-symbols-outlined text-[16px] text-on-surface-variant">print</span>
                                                Cetak Struk
                                            </a>
                                            @if ($order->member)
                                                @php
                                                    $waPhone = preg_replace('/\D+/', '', $order->member->phone ?? '');
                                                    if (str_starts_with($waPhone, '0')) {
                                                        $waPhone = '62' . ltrim($waPhone, '0');
                                                    } elseif (!str_starts_with($waPhone, '62')) {
                                                        $waPhone = '62' . $waPhone;
                                                    }
                                                @endphp
                                                <a href="https://wa.me/{{ $waPhone }}?text=Halo {{ urlencode($order->member->name) }}, order Anda ({{ $order->order_number }}) sudah {{ $order->status_label }}."
                                                    target="_blank" @click="open=false"
                                                    class="w-full px-3 py-2.5 text-xs text-left hover:bg-surface-container flex items-center gap-2 transition-colors">
                                                    <span
                                                        class="material-symbols-outlined text-[16px] text-green-600">chat</span>
                                                    Kirim WA
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Quick status advance --}}
                                    @if ($status !== 'selesai')
                                        @php
                                            $nextStatus = match ($status) {
                                                'baru' => 'dicuci',
                                                'dicuci' => 'disetrika',
                                                'disetrika' => 'siap_diambil',
                                                'siap_diambil' => 'selesai',
                                                default => null,
                                            };
                                            $nextLabel = match ($status) {
                                                'baru' => 'Mulai Cuci',
                                                'dicuci' => 'Setrika',
                                                'disetrika' => 'Siap Ambil',
                                                'siap_diambil' => 'Selesai',
                                                default => null,
                                            };
                                        @endphp
                                        @if ($nextStatus && !($nextStatus === 'selesai' && $order->payment_status !== 'lunas'))
                                            @if ($nextStatus === 'siap_diambil' && $isWaitingWeight)
                                                <button
                                                    @click="$dispatch('open-weigh-modal', { orderId: {{ $order->id }}, pendingStatus: 'siap_diambil' })"
                                                    class="px-2.5 py-1 bg-primary-container hover:bg-[#e08e0b] text-white rounded-lg text-[10px] font-bold transition-colors flex items-center gap-1">
                                                    {{ $nextLabel }} →
                                                </button>
                                            @else
                                                <button
                                                    wire:click="updateStatus({{ $order->id }}, '{{ $nextStatus }}')"
                                                    class="px-2.5 py-1 bg-primary-container hover:bg-[#e08e0b] text-white rounded-lg text-[10px] font-bold transition-colors flex items-center gap-1">
                                                    {{ $nextLabel }} →
                                                </button>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-on-surface-variant opacity-60">
                            <span class="material-symbols-outlined text-2xl block mb-1">inbox</span>
                            Tidak ada order
                        </div>
                    @endforelse

                    {{-- Tombol lihat lebih / sembunyikan --}}
                    @if ($hiddenCount > 0)
                        <button @click="showAll = !showAll"
                            class="w-full py-2 rounded-xl text-xs font-semibold border transition-colors"
                            :class="showAll
                                ?
                                'bg-white border-outline-variant text-on-surface-variant hover:bg-surface-container' :
                                'bg-surface-container border-primary-container/40 text-[#865300] hover:bg-surface-container-high'">
                            <span x-show="!showAll">+ {{ $hiddenCount }} order lainnya</span>
                            <span x-show="showAll" x-cloak>↑ Sembunyikan</span>
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Weigh Modal --}}
    <div x-data="{
        show: false,
        orderId: null,
        weight: 0,
        total: 0,
        itemPrice: 0,
        itemName: '',
        pendingStatus: null,
        calculateTotal() {
            this.total = this.weight * this.itemPrice;
        }
    }"
        @open-weigh-modal.window="
        show = true;
        orderId = $event.detail.orderId;
        pendingStatus = $event.detail.pendingStatus ?? null;
        let groupedOrders = Object.values(@js($this->orders->toArray()));
        let order = groupedOrders.flat().find(o => o.id === orderId);
        if (order) {
            let kiloanItem = order.items.find(item => item.service_type === 'kiloan');
            if (kiloanItem) {
                itemName = kiloanItem.service_name;
                itemPrice = kiloanItem.price;
                weight = kiloanItem.weight ?? kiloanItem.quantity;
                calculateTotal();
            }
        }
    "
        x-show="show" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div @click.outside="show = false" class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 m-4">
            <h3 class="text-lg font-bold mb-1">Update Berat Laundry Kiloan</h3>
            <p class="text-sm text-on-surface-variant mb-4" x-text="`Order: ${itemName}`"></p>

            <div class="space-y-4">
                <div>
                    <label for="weight" class="text-sm font-medium text-on-surface-variant">Berat (kg)</label>
                    <input type="number" step="0.1" x-model="weight" @input="calculateTotal"
                        class="w-full mt-1 border border-outline-variant rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary-container focus:outline-none">
                </div>
                <div>
                    <label class="text-sm font-medium text-on-surface-variant">Harga per kg</label>
                    <p class="text-sm" x-text="`Rp ${itemPrice.toLocaleString('id-ID')}`"></p>
                </div>
                <div>
                    <label class="text-sm font-medium text-on-surface-variant">Total Harga Item</label>
                    <p class="text-lg font-bold text-primary-container"
                        x-text="`Rp ${total.toLocaleString('id-ID')}`">
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button @click="show = false"
                    class="px-4 py-2 text-sm font-medium text-on-surface-variant bg-gray-100 rounded-xl hover:bg-gray-200">
                    Batal
                </button>
                <button
                    @click="$wire.updateWeight(orderId, weight).then(() => {
                        if (pendingStatus) {
                            $wire.updateStatus(orderId, pendingStatus).then(() => { show = false; pendingStatus = null; });
                        } else {
                            show = false;
                        }
                    })"
                    class="px-4 py-2 text-sm font-medium text-white bg-primary-container rounded-xl hover:bg-[#e08e0b]">
                    Simpan
                </button>
            </div>
        </div>
    </div>

    <div x-data="{
        show: false,
        orderId: null,
        orderNumber: '',
        paymentMethod: 'tunai'
    }"
        @open-payment-modal.window="
        show = true;
        orderId = $event.detail.orderId;
        orderNumber = $event.detail.orderNumber;
        paymentMethod = 'tunai';
    "
        x-show="show" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div @click.outside="show = false" class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 m-4">
            <h3 class="text-lg font-bold mb-1">Tandai Lunas</h3>
            <p class="text-sm text-on-surface-variant mb-4" x-text="`Pilih metode bayar untuk ${orderNumber}`"></p>

            <div class="grid grid-cols-3 gap-2">
                @foreach ($paymentMethods as $value => $label)
                    <button type="button" @click="paymentMethod = '{{ $value }}'"
                        :class="paymentMethod === '{{ $value }}' ?
                            'bg-primary-container text-white border-primary-container' :
                            'bg-white text-on-surface-variant border-outline-variant'"
                        class="border rounded-xl px-3 py-2 text-sm font-medium transition-colors">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button @click="show = false"
                    class="px-4 py-2 text-sm font-medium text-on-surface-variant bg-gray-100 rounded-xl hover:bg-gray-200">
                    Batal
                </button>
                <button @click="$wire.markPaid(orderId, paymentMethod).then(() => { show = false })"
                    class="px-4 py-2 text-sm font-medium text-white bg-primary-container rounded-xl hover:bg-[#e08e0b]">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
