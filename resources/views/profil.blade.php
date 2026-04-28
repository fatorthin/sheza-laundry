@extends('layouts.app')
@section('title', 'Profil – Sheza Laundry')
@section('content')
    <div>
        <header class="flex items-center gap-3 px-4 py-3 bg-white border-b border-outline-variant">
            <span class="material-symbols-outlined text-primary-container filled text-2xl">local_laundry_service</span>
            <span class="font-bold text-primary-container text-lg">Sheza Laundry</span>
        </header>

        <div class="px-4 pt-8 pb-4 flex flex-col items-center text-center">
            <div class="w-20 h-20 rounded-full bg-surface-container flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-primary-container filled" style="font-size:40px">person</span>
            </div>

            @auth
                <p class="font-bold text-lg text-[#221a12]">{{ auth()->user()->name }}</p>
                <p class="text-sm text-on-surface-variant mb-6">{{ auth()->user()->email }}</p>

                <a href="{{ route('admin.dashboard') }}"
                    class="w-full flex items-center gap-3 px-4 py-3 bg-primary-container text-white rounded-xl font-medium text-sm hover:bg-[#e08e0b] transition-colors mb-3">
                    <span class="material-symbols-outlined text-[20px]">dashboard</span>
                    Buka Panel Admin
                </a>

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-3 bg-white border border-outline-variant text-red-500 rounded-xl font-medium text-sm hover:bg-red-50 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                        Keluar
                    </button>
                </form>
            @else
                <p class="font-bold text-lg text-[#221a12] mb-1">Halo, Tamu!</p>
                <p class="text-sm text-on-surface-variant mb-8">Masuk untuk mengakses panel admin Sheza Laundry.</p>

                <a href="{{ route('login') }}"
                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-primary-container text-white rounded-xl font-medium text-sm hover:bg-[#e08e0b] transition-colors">
                    <span class="material-symbols-outlined text-[20px]">login</span>
                    Masuk sebagai Admin
                </a>
            @endauth
        </div>

        <div class="mx-4 mt-4 bg-white rounded-2xl border border-outline-variant overflow-hidden">
            <div class="px-4 py-3 border-b border-[#f0e0d2]">
                <p class="text-xs font-semibold uppercase tracking-wide text-on-surface-variant">Informasi Toko</p>
            </div>
            <div class="divide-y divide-[#f0e0d2]">
                <div class="flex items-center gap-3 px-4 py-3">
                    <span class="material-symbols-outlined text-primary-container text-[20px]">location_on</span>
                    <p class="text-sm text-[#221a12]">Jl. Contoh No. 123, Jakarta</p>
                </div>
                <div class="flex items-center gap-3 px-4 py-3">
                    <span class="material-symbols-outlined text-primary-container text-[20px]">phone</span>
                    <a href="tel:+6281234567890" class="text-sm text-[#221a12]">+62 812-3456-7890</a>
                </div>
                <div class="flex items-center gap-3 px-4 py-3">
                    <span class="material-symbols-outlined text-primary-container text-[20px]">schedule</span>
                    <p class="text-sm text-[#221a12]">Senin–Sabtu: 08:00–20:00 WIB</p>
                </div>
                <a href="https://wa.me/6281234567890" target="_blank" rel="noopener"
                    class="flex items-center gap-3 px-4 py-3 hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined text-green-500 text-[20px]">chat</span>
                    <p class="text-sm text-[#221a12]">Hubungi via WhatsApp</p>
                    <span class="material-symbols-outlined text-on-surface-variant text-[16px] ml-auto">chevron_right</span>
                </a>
            </div>
        </div>
    </div>
@endsection
