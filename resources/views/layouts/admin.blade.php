<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sheza Laundry') – Admin</title>
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="h-full bg-[#fff8f4] text-[#221a12] font-sans antialiased" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar --}}
        <nav class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-outline-variant flex flex-col
                transform transition-transform duration-200 ease-in-out
                -translate-x-full md:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

            {{-- Brand --}}
            <div class="h-16 flex items-center gap-3 px-5 border-b border-outline-variant">
                <div class="w-9 h-9 rounded-full bg-primary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-lg filled">local_laundry_service</span>
                </div>
                <div>
                    <p class="font-bold text-[#865300] leading-tight text-sm">Sheza Laundry</p>
                    <p class="text-[11px] text-on-surface-variant">Admin Panel</p>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">
                @php
                    $navItems = [
                        ['route' => 'admin.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
                        ['route' => 'admin.pos', 'icon' => 'point_of_sale', 'label' => 'Kasir (POS)'],
                        ['route' => 'admin.orders', 'icon' => 'receipt_long', 'label' => 'Manajemen Order'],
                        ['route' => 'admin.members', 'icon' => 'group', 'label' => 'Pelanggan'],
                        ['route' => 'admin.services', 'icon' => 'category', 'label' => 'Layanan'],
                    ];
                @endphp
                @foreach ($navItems as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                          {{ $active ? 'bg-surface-container text-primary-container border-r-2 border-primary-container' : 'text-on-surface-variant hover:bg-surface-container hover:text-[#865300]' }}">
                        <span class="material-symbols-outlined text-[20px] {{ $active ? 'filled' : '' }}">{{ $item['icon'] }}</span>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>

            {{-- Footer --}}
            <div class="p-3 border-t border-outline-variant space-y-0.5">
                <a href="{{ url('/') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-on-surface-variant hover:bg-surface-container hover:text-[#865300] transition-colors">
                    <span class="material-symbols-outlined text-[20px]">home</span>
                    Halaman Utama
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-on-surface-variant hover:bg-red-50 hover:text-red-600 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                        Keluar
                    </button>
                </form>
            </div>
        </nav>

        {{-- Backdrop (mobile) --}}
        <div class="fixed inset-0 z-40 bg-black/40 md:hidden" x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity duration-200" x-transition:leave="transition-opacity duration-200" style="display:none"></div>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-w-0 md:ml-64">

            {{-- TopBar --}}
            <header class="h-16 flex items-center justify-between px-4 bg-white border-b border-outline-variant sticky top-0 z-30 shadow-sm">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <span class="font-bold text-primary-container md:hidden">Sheza Laundry</span>
                </div>
                <div class="hidden md:flex items-center gap-2 text-sm text-on-surface-variant">
                    <span class="material-symbols-outlined text-[18px]">schedule</span>
                    <span id="clock"></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-on-surface-variant hidden md:block">{{ auth()->user()->name }}</span>
                    <div class="w-9 h-9 rounded-full bg-primary-container flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
            </header>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mx-4 mt-4 p-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-center gap-2" x-data x-init="setTimeout(() => $el.remove(), 3000)">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mx-4 mt-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm flex items-center gap-2" x-data x-init="setTimeout(() => $el.remove(), 4000)">
                    <span class="material-symbols-outlined text-[18px]">error</span>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toLocaleString('id-ID', {
                weekday: 'short',
                day: 'numeric',
                month: 'short',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        updateClock();
        setInterval(updateClock, 1000);
    </script>

    @livewireScripts
    @stack('scripts')
</body>

</html>
