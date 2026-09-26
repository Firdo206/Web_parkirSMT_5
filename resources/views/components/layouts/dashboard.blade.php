<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} — ParkVisi Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F4F6F8] font-['Instrument_Sans'] text-[#101820]">

<div class="flex min-h-screen" x-data="{ sidebarOpen: false }">

    {{-- SIDEBAR --}}
    <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-[#0B1D33] text-white flex flex-col transition-transform duration-200 -translate-x-full lg:translate-x-0"
           :class="{ '!translate-x-0': sidebarOpen }">

        <div class="flex items-center gap-3 px-6 h-20 border-b border-white/10">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                <rect width="28" height="28" rx="7" fill="#00C2A8"/>
                <path d="M9 20V8h5.2a4 4 0 010 8H9" stroke="#0B1D33" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span class="font-['Sora'] font-bold text-lg">ParkVisi</span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-white/40 mb-2">Menu Utama</p>

            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                      {{ request()->routeIs('dashboard') ? 'bg-[#00C2A8]/15 text-[#00C2A8]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                    <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                    <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                    <rect x="3" y="16" width="7" height="5" rx="1.5"/>
                </svg>
                Dashboard
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/70 hover:bg-white/5 hover:text-white transition">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 4a4 4 0 100 8 4 4 0 000-8zM6 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/>
                </svg>
                Kendaraan Terdaftar
            </a>

            <a href="{{ route('admin.paket-harga.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                      {{ request()->routeIs('admin.paket-harga.*') ? 'bg-[#00C2A8]/15 text-[#00C2A8]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 3v18h18M7 15l4-6 3 3 4-7"/>
                </svg>
                Paket Harga
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/70 hover:bg-white/5 hover:text-white transition">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 9v4l2 2M12 3a9 9 0 100 18 9 9 0 000-18z"/>
                </svg>
                Riwayat Kejadian
            </a>

            <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-white/40 mt-6 mb-2">Akun</p>

            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/70 hover:bg-white/5 hover:text-white transition">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 20c0-4.4 3.6-8 8-8s8 3.6 8 8"/>
                </svg>
                Profil Saya
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/70 hover:bg-red-500/10 hover:text-red-400 transition">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </nav>

        <div class="px-4 py-4 border-t border-white/10">
            <div class="flex items-center gap-3 px-2">
                <div class="w-9 h-9 rounded-full bg-[#00C2A8] flex items-center justify-center font-['Sora'] font-bold text-sm">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-white/50 truncate">{{ auth()->user()->email ?? '' }}</p>
                </div>
            </div>
        </div>
    </aside>

    {{-- OVERLAY MOBILE --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 flex flex-col lg:ml-64 min-w-0">

        {{-- TOP BAR --}}
        <header class="sticky top-0 z-20 bg-white border-b border-[#E4E9EE] h-20 flex items-center justify-between px-6">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-[#5B6B7A]">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div>
                    <h1 class="font-['Sora'] font-bold text-xl text-[#0B1D33]">{{ $title ?? 'Dashboard' }}</h1>
                    <p class="text-sm text-[#5B6B7A]">{{ $subtitle ?? 'Selamat datang kembali!' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button class="relative w-10 h-10 rounded-lg border border-[#E4E9EE] flex items-center justify-center text-[#5B6B7A] hover:bg-[#F4F6F8] transition">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 01-3.4 0"/>
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-[#FFB100] rounded-full"></span>
                </button>
                <div class="w-9 h-9 rounded-full bg-[#0B1D33] text-white flex items-center justify-center font-['Sora'] font-bold text-sm">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="flex-1 p-6">
            {{ $slot }}
        </main>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js" defer></script>
</body>
</html>