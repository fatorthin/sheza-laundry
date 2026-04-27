@extends('layouts.app')
@section('title', 'Sheza Laundry')
@section('content')
<div>
  <header class="flex items-center justify-between px-4 py-3 bg-white border-b border-[#d8c3ad]">
    <div class="flex items-center gap-2">
      <span class="material-symbols-outlined text-[#f39c12] filled text-2xl">local_laundry_service</span>
      <span class="font-bold text-[#f39c12] text-lg">Sheza Laundry</span>
    </div>
    <a href="{{ route('login') }}" class="text-[#534434] hover:text-[#f39c12]">
      <span class="material-symbols-outlined">manage_accounts</span>
    </a>
  </header>

  <!-- Hero -->
  <div class="mx-4 mt-4 bg-gradient-to-br from-[#fbebdd] to-[#f5e5d7] rounded-2xl p-6 relative overflow-hidden">
    <div class="absolute right-0 top-0 w-24 h-24 bg-[#f39c12]/10 rounded-full -translate-y-4 translate-x-4"></div>
    <div class="inline-flex w-14 h-14 rounded-full bg-white/60 items-center justify-center mb-4">
      <span class="material-symbols-outlined text-[#865300] text-3xl filled">dry_cleaning</span>
    </div>
    <h1 class="text-2xl font-black text-[#221a12] leading-tight mb-2">Perawatan Premium<br>untuk Pakaian Anda</h1>
    <p class="text-sm text-[#534434] mb-5">Cepat, terpercaya, dan rapi sempurna. Kami urus laundry Anda.</p>
    <button id="pwa-install-btn" onclick="installPWA()"
            class="hidden w-full py-3 bg-[#f39c12] hover:bg-[#e08e0b] text-white font-semibold rounded-xl
                   flex items-center justify-center gap-2 transition-all active:scale-[0.98]">
      <span class="material-symbols-outlined text-[20px]">download</span>
      Install Aplikasi
    </button>
    <div id="pwa-default-btn" class="w-full py-3 bg-white/60 rounded-xl text-center text-sm text-[#534434]">
      ✨ Layanan Laundry Terpercaya
    </div>
  </div>

  <!-- Track Order -->
  <div class="mx-4 mt-4 bg-white rounded-2xl p-4 shadow-sm border border-[#d8c3ad]">
    @livewire('order-tracker')
  </div>

  <!-- Services -->
  <div class="px-4 mt-6">
    <div class="flex items-center justify-between mb-3">
      <h2 class="text-base font-bold">Layanan Kami</h2>
    </div>
    <div class="grid grid-cols-2 gap-3">
      @foreach($services->take(4) as $service)
      <div class="bg-white rounded-2xl p-4 shadow-sm border border-[#d8c3ad] relative overflow-hidden">
        <div class="w-10 h-10 rounded-full bg-[#fbebdd] flex items-center justify-center mb-3">
          <span class="material-symbols-outlined text-[#f39c12] text-[20px] filled">{{ $service->icon }}</span>
        </div>
        <p class="font-semibold text-sm text-[#221a12]">{{ $service->name }}</p>
        <p class="text-xs text-[#534434] mt-0.5">{{ ucfirst($service->category) }}</p>
        <p class="text-xs text-[#f39c12] font-semibold mt-1">
          Mulai Rp {{ number_format($service->price, 0, ',', '.') }}/{{ $service->unit }}
        </p>
      </div>
      @endforeach
    </div>
  </div>

  <!-- Footer -->
  <div class="mx-4 mt-6 mb-4 bg-[#221a12] rounded-2xl p-5 text-white">
    <p class="font-bold text-[#f39c12] mb-2">Sheza Laundry</p>
    <p class="text-xs text-gray-300 mb-1">📍 Jl. Contoh No. 123, Jakarta</p>
    <p class="text-xs text-gray-300 mb-1">📞 +62 812-3456-7890</p>
    <p class="text-xs text-gray-300">⏰ Senin–Sabtu: 08:00–20:00 WIB</p>
  </div>

  <!-- WhatsApp FAB -->
  <a href="https://wa.me/6281234567890" target="_blank" rel="noopener"
     class="fixed bottom-24 right-4 w-14 h-14 bg-green-500 hover:bg-green-600 rounded-full flex items-center justify-center shadow-lg z-40 transition-transform hover:scale-110">
    <svg viewBox="0 0 24 24" class="w-7 h-7 fill-white">
      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
    </svg>
  </a>
</div>
@endsection