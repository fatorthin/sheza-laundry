@extends('layouts.admin')
@section('title', 'Detail Order #' . $order->order_number)
@section('content')
    <div class="max-w-2xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.orders') }}" class="text-on-surface-variant hover:text-primary-container">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-xl font-bold">{{ $order->order_number }}</h1>
                <p class="text-sm text-on-surface-variant">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
            </div>
            <div class="ml-auto flex gap-2">
                <a href="{{ route('admin.receipt', $order) }}" target="_blank"
                    class="flex items-center gap-1 px-3 py-2 bg-primary-container text-white rounded-xl text-sm font-medium hover:bg-[#e08e0b] transition-colors">
                    <span class="material-symbols-outlined text-[18px]">print</span> Cetak
                </a>
            </div>
        </div>

        <div class="grid gap-4">
            <!-- Status & Payment -->
            <div class="bg-white rounded-2xl border border-outline-variant p-5">
                <div class="flex items-center justify-between mb-4">
                    <span
                        class="px-3 py-1 rounded-full text-sm font-semibold
          {{ match ($order->status) {
              'baru' => 'bg-blue-100 text-blue-700',
              'dikerjakan' => 'bg-yellow-100 text-yellow-700',
              'siap_diambil' => 'bg-green-100 text-green-700',
              'selesai' => 'bg-gray-100 text-gray-600',
              default => 'bg-gray-100 text-gray-600',
          } }}">{{ $order->status_label }}</span>
                    <span
                        class="px-3 py-1 rounded-full text-sm font-semibold
          {{ $order->payment_status === 'lunas' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                        {{ $order->payment_status === 'lunas' ? 'Lunas' : 'Belum Bayar' }}
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="flex gap-2">
                    @csrf
                    <select name="status"
                        class="flex-1 border border-outline-variant rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary-container focus:border-primary-container">
                        @foreach (['baru' => 'Baru', 'dikerjakan' => 'Dikerjakan', 'siap_diambil' => 'Siap Diambil', 'selesai' => 'Selesai'] as $val => $label)
                            <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="px-4 py-2 bg-primary-container text-white rounded-xl text-sm font-medium hover:bg-[#e08e0b]">Update</button>
                </form>
            </div>

            <!-- Customer Info -->
            <div class="bg-white rounded-2xl border border-outline-variant p-5">
                <h3 class="font-semibold mb-3 text-sm uppercase tracking-wide text-on-surface-variant">Pelanggan</h3>
                @if ($order->member)
                    <p class="font-semibold">{{ $order->member->name }}</p>
                    <p class="text-sm text-on-surface-variant">{{ $order->member->phone }}</p>
                @else
                    <p class="text-sm text-on-surface-variant">Tamu (tidak terdaftar)</p>
                @endif
            </div>

            <!-- Items -->
            <div class="bg-white rounded-2xl border border-outline-variant p-5">
                <h3 class="font-semibold mb-3 text-sm uppercase tracking-wide text-on-surface-variant">Item Order</h3>
                <div class="space-y-3">
                    @foreach ($order->items as $item)
                        <div class="flex justify-between text-sm">
                            <div>
                                <p class="font-medium">{{ $item->service_name }}</p>
                                @if ($item->service_type === 'kiloan')
                                    @if ((float) ($item->weight ?? 0) > 0)
                                        <p class="text-xs text-on-surface-variant">
                                            {{ number_format($item->weight, 2, ',', '.') }}kg
                                            × Rp {{ number_format($item->price, 0, ',', '.') }}/kg</p>
                                    @else
                                        <p class="text-xs text-primary-container">Rp
                                            {{ number_format($item->price, 0, ',', '.') }}/kg — Menunggu Timbang</p>
                                    @endif
                                @else
                                    <p class="text-xs text-on-surface-variant">{{ $item->quantity }}x × Rp
                                        {{ number_format($item->price, 0, ',', '.') }}</p>
                                @endif
                            </div>
                            <p class="font-semibold">
                                @if ($item->service_type === 'kiloan' && (float) ($item->weight ?? 0) <= 0)
                                    TBD
                                @elseif($item->service_type === 'kiloan')
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                @else
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Weight Finalization (for kiloan orders) -->
            @if ($order->has_kiloan && $order->status === 'dikerjakan')
                <div class="bg-orange-50 rounded-2xl border border-orange-200 p-5">
                    <h3 class="font-semibold mb-2 text-orange-800">Input Berat Akhir</h3>
                    <p class="text-xs text-orange-700 mb-3">Setelah pencucian selesai, masukkan berat aktual untuk
                        menghitung total.</p>
                    <form method="POST" action="{{ route('admin.orders.weight', $order) }}" class="flex gap-2">
                        @csrf
                        <input type="number" name="weight" step="0.1" min="0.1" placeholder="Berat (kg)"
                            class="flex-1 border border-orange-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary-container">
                        <button type="submit"
                            class="px-4 py-2 bg-primary-container text-white rounded-xl text-sm font-medium">Simpan</button>
                    </form>
                </div>
            @endif

            <!-- Totals -->
            <div class="bg-white rounded-2xl border border-outline-variant p-5">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-on-surface-variant">Subtotal</span><span>Rp
                            {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between font-bold text-base pt-2 border-t border-outline-variant">
                        <span>Total</span>
                        <span class="text-primary-container">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
