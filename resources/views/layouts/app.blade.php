<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#f39c12">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Sheza Laundry">
    <title>@yield('title', 'Sheza Laundry')</title>
    <link rel="manifest" href="/manifest.json">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#fff8f4] text-[#221a12] font-sans antialiased min-h-screen flex flex-col">
    <main class="flex-1 pb-20">@yield('content')</main>
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-[#d8c3ad] pb-safe z-50">
        <div class="flex">
            <a href="{{ route('landing') }}" class="flex-1 flex flex-col items-center py-2 text-xs {{ request()->routeIs('landing') ? 'text-[#f39c12]' : 'text-[#534434]' }}">
                <span class="material-symbols-outlined text-[22px] mb-0.5 {{ request()->routeIs('landing') ? 'filled' : '' }}">home</span>
                Beranda
            </a>
            <a href="#" class="flex-1 flex flex-col items-center py-2 text-xs text-[#534434]">
                <span class="material-symbols-outlined text-[22px] mb-0.5">dry_cleaning</span>
                Layanan
            </a>
            <a href="#" class="flex-1 flex flex-col items-center py-2 text-xs text-[#534434]">
                <span class="material-symbols-outlined text-[22px] mb-0.5">receipt_long</span>
                Lacak
            </a>
            <a href="#" class="flex-1 flex flex-col items-center py-2 text-xs text-[#534434]">
                <span class="material-symbols-outlined text-[22px] mb-0.5">person</span>
                Profil
            </a>
        </div>
    </nav>
    @livewireScripts
    @stack('scripts')
    <script>
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            deferredPrompt = e;
            const btn = document.getElementById('pwa-install-btn');
            if (btn) btn.classList.remove('hidden');
        });
        function installPWA() {
            if (deferredPrompt) { deferredPrompt.prompt(); deferredPrompt.userChoice.then(() => { deferredPrompt = null; }); }
        }
        if ('serviceWorker' in navigator) { navigator.serviceWorker.register('/sw.js').catch(() => {}); }
    </script>
</body>
</html>
