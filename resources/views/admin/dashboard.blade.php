@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-[#221a12]">Overview</h1>
            <p class="text-sm text-on-surface-variant">Selamat datang. Ini yang terjadi hari ini.</p>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-surface-container rounded-2xl p-5 relative overflow-hidden">
                <div class="absolute -right-3 -top-3 w-16 h-16 bg-primary-container/20 rounded-full"></div>
                <span class="material-symbols-outlined text-primary-container filled text-2xl">receipt_long</span>
                <p class="text-2xl font-bold mt-2">{{ $newOrders }}</p>
                <p class="text-xs text-on-surface-variant">Order Baru</p>
            </div>
            <div class="bg-surface-container rounded-2xl p-5 relative overflow-hidden">
                <div class="absolute -right-3 -top-3 w-16 h-16 bg-green-200/40 rounded-full"></div>
                <span class="material-symbols-outlined text-green-600 filled text-2xl">payments</span>
                <p class="text-xl font-bold mt-2">Rp {{ number_format($revenueToday, 0, ',', '.') }}</p>
                <p class="text-xs text-on-surface-variant">Pendapatan Hari Ini</p>
            </div>
            <div class="bg-surface-container rounded-2xl p-5 relative overflow-hidden">
                <div class="absolute -right-3 -top-3 w-16 h-16 bg-orange-200/40 rounded-full"></div>
                <span class="material-symbols-outlined text-orange-500 filled text-2xl">pending_actions</span>
                <p class="text-2xl font-bold mt-2">{{ $pendingPickup }}</p>
                <p class="text-xs text-on-surface-variant">Siap Diambil</p>
            </div>
            <div class="bg-surface-container rounded-2xl p-5 relative overflow-hidden">
                <div class="absolute -right-3 -top-3 w-16 h-16 bg-blue-200/40 rounded-full"></div>
                <span class="material-symbols-outlined text-blue-600 filled text-2xl">group</span>
                <p class="text-2xl font-bold mt-2">{{ $totalMembers }}</p>
                <p class="text-xs text-on-surface-variant">Total Pelanggan</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <a href="{{ route('admin.pos') }}"
                class="flex items-center gap-3 p-4 bg-primary-container text-white rounded-2xl hover:bg-[#e08e0b] transition-colors">
                <span class="material-symbols-outlined filled text-2xl">point_of_sale</span>
                <div>
                    <p class="font-semibold text-sm">Buka Kasir</p>
                    <p class="text-xs opacity-80">Proses order baru</p>
                </div>
            </a>
            <a href="{{ route('admin.orders') }}"
                class="flex items-center gap-3 p-4 bg-white border border-outline-variant text-[#221a12] rounded-2xl hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-primary-container text-2xl">receipt_long</span>
                <div>
                    <p class="font-semibold text-sm">Manajemen Order</p>
                    <p class="text-xs text-on-surface-variant">Lihat semua order</p>
                </div>
            </a>
            <a href="{{ route('admin.members') }}"
                class="flex items-center gap-3 p-4 bg-white border border-outline-variant text-[#221a12] rounded-2xl hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-primary-container text-2xl">group</span>
                <div>
                    <p class="font-semibold text-sm">Pelanggan</p>
                    <p class="text-xs text-on-surface-variant">Kelola database</p>
                </div>
            </a>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-2xl border border-outline-variant overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b border-outline-variant">
                <h2 class="font-semibold">Order Terbaru</h2>
                <a href="{{ route('admin.orders') }}" class="text-xs text-primary-container font-medium">Lihat Semua →</a>
            </div>
            @forelse($recentOrders as $order)
                <div
                    class="flex items-center gap-3 p-4 border-b border-[#f0e0d2] last:border-0 hover:bg-surface-container/50 transition-colors">
                    <div
                        class="w-9 h-9 rounded-full bg-primary-container flex items-center justify-center text-white font-bold text-sm shrink-0">
                        {{ strtoupper(substr($order->member?->name ?? 'G', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-sm truncate">{{ $order->member?->name ?? 'Tamu' }}</p>
                        <p class="text-xs text-on-surface-variant">{{ $order->order_number }}</p>
                    </div>
                    <div class="text-right">
                        <span
                            class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold
          {{ match ($order->status) {
              'baru' => 'bg-blue-100 text-blue-700',
              'dicuci' => 'bg-yellow-100 text-yellow-700',
              'disetrika' => 'bg-orange-100 text-orange-700',
              'siap_diambil' => 'bg-green-100 text-green-700',
              'selesai' => 'bg-gray-100 text-gray-600',
              default => 'bg-gray-100 text-gray-600',
          } }}">{{ $order->status_label }}</span>
                        <p class="text-xs font-semibold mt-1">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-on-surface-variant text-sm">Belum ada order hari ini.</div>
            @endforelse
        </div>
    </div>
@endsection
