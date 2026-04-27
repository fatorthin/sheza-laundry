<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login – Sheza Laundry</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-[#fff8f4] text-[#221a12] font-sans antialiased flex">

    <div class="flex flex-1 flex-col md:flex-row w-full min-h-screen">

        {{-- Left: Form --}}
        <div class="flex-1 flex flex-col justify-center items-center p-8 bg-[#fff8f4]">
            <div
                class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 border border-[#d8c3ad] relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-[#f39c12]"></div>
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#fbebdd] mb-4">
                        <span
                            class="material-symbols-outlined text-[#f39c12] text-3xl filled">local_laundry_service</span>
                    </div>
                    <h1 class="text-2xl font-bold text-[#865300]">Sheza Laundry</h1>
                    <p class="text-sm text-[#534434] mt-1">Masuk ke akun Anda</p>
                </div>

                <form method="POST" action="{{ route('login.attempt') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-[#221a12] mb-1.5 uppercase tracking-wide">Email
                            atau No. HP</label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#534434] text-[18px]">person</span>
                            <input type="text" name="email" value="{{ old('email') }}" required autofocus
                                class="w-full pl-10 pr-4 py-3 border border-[#d8c3ad] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#f39c12] focus:border-[#f39c12] bg-white transition-colors @error('email') border-red-400 @enderror"
                                placeholder="Masukkan email atau nomor HP">
                        </div>
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold text-[#221a12] mb-1.5 uppercase tracking-wide">Password</label>
                        <div class="relative" x-data="{ show: false }">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#534434] text-[18px]">lock</span>
                            <input :type="show ? 'text' : 'password'" name="password" required
                                class="w-full pl-10 pr-10 py-3 border border-[#d8c3ad] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#f39c12] focus:border-[#f39c12] bg-white transition-colors @error('password') border-red-400 @enderror"
                                placeholder="••••••••">
                            <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-[#534434] hover:text-[#865300]">
                                <span class="material-symbols-outlined text-[18px]"
                                    x-text="show ? 'visibility' : 'visibility_off'">visibility_off</span>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-[#534434] cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-[#d8c3ad] text-[#f39c12]">
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-[#f39c12] hover:bg-[#e08e0b] text-white font-semibold rounded-xl transition-all active:scale-[0.98] shadow-sm">
                        Login
                    </button>
                </form>

                <div class="mt-6 pt-5 border-t border-[#f0e0d2] text-center text-xs text-[#534434]">
                    <a href="{{ url('/') }}" class="hover:text-[#f39c12] transition-colors">← Kembali ke Halaman
                        Utama</a>
                </div>
            </div>
        </div>

        {{-- Right: Image (hidden on mobile) --}}
        <div class="hidden md:flex flex-1 relative bg-[#f5e5d7] overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-[#f39c12]/30 to-[#865300]/20"></div>
            <div class="relative z-10 flex flex-col justify-end p-12 w-full">
                <div class="bg-white/80 backdrop-blur-sm p-8 rounded-2xl border border-white/50 max-w-lg">
                    <h2 class="text-2xl font-bold text-[#865300] mb-3">Freshness Delivered.</h2>
                    <p class="text-[#534434] text-sm leading-relaxed">Kelola laundry Anda dengan mudah dan efisien.
                        Lacak order, kelola pelanggan, dan cetak struk — semua dalam satu platform.</p>
                </div>
            </div>
        </div>

    </div>
</body>

</html>
