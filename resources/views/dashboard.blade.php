<x-layouts.dashboard title="Dashboard" subtitle="Ringkasan aktivitas parkir hari ini">

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

        <div class="bg-white rounded-2xl border border-[#E4E9EE] p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-[#00C2A8]/10 flex items-center justify-center text-[#00C2A8]">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 4a4 4 0 100 8 4 4 0 000-8zM6 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-[#00C2A8] bg-[#00C2A8]/10 px-2 py-1 rounded-full">+12%</span>
            </div>
            <p class="text-2xl font-['Sora'] font-bold text-[#0B1D33]">1.284</p>
            <p class="text-sm text-[#5B6B7A] mt-1">Kendaraan Terdaftar</p>
        </div>

        <div class="bg-white rounded-2xl border border-[#E4E9EE] p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-[#FFB100]/10 flex items-center justify-center text-[#FFB100]">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="7" width="18" height="13" rx="2"/>
                        <path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-[#0B1D33] bg-[#F4F6F8] px-2 py-1 rounded-full">Aktif</span>
            </div>
            <p class="text-2xl font-['Sora'] font-bold text-[#0B1D33]">18</p>
            <p class="text-sm text-[#5B6B7A] mt-1">Lokasi Parkir Mitra</p>
        </div>

        <div class="bg-white rounded-2xl border border-[#E4E9EE] p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-[#0B1D33]/10 flex items-center justify-center text-[#0B1D33]">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 3v18h18M7 15l4-6 3 3 4-7"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-[#00C2A8] bg-[#00C2A8]/10 px-2 py-1 rounded-full">+8%</span>
            </div>
            <p class="text-2xl font-['Sora'] font-bold text-[#0B1D33]">Rp 42,5jt</p>
            <p class="text-sm text-[#5B6B7A] mt-1">Pemasukan Hari Ini</p>
        </div>

        <div class="bg-white rounded-2xl border border-[#E4E9EE] p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 9v4M12 17h.01M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-red-500 bg-red-500/10 px-2 py-1 rounded-full">3 baru</span>
            </div>
            <p class="text-2xl font-['Sora'] font-bold text-[#0B1D33]">7</p>
            <p class="text-sm text-[#5B6B7A] mt-1">Gagal Verifikasi</p>
        </div>
    </div>

    {{-- CONTENT GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 bg-white rounded-2xl border border-[#E4E9EE] p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-['Sora'] font-bold text-lg text-[#0B1D33]">Aktivitas Terbaru</h2>
                <a href="#" class="text-sm font-medium text-[#00C2A8] hover:underline">Lihat semua</a>
            </div>

            <div class="space-y-1">
                @php
                    $activities = [
                        ['plat' => 'B 1234 XYZ', 'lokasi' => 'Grand City Mall', 'waktu' => '2 menit lalu', 'status' => 'masuk'],
                        ['plat' => 'D 5678 ABC', 'lokasi' => 'Menara Kuningan', 'waktu' => '8 menit lalu', 'status' => 'keluar'],
                        ['plat' => 'B 9012 QWE', 'lokasi' => 'Grand City Mall', 'waktu' => '15 menit lalu', 'status' => 'gagal'],
                        ['plat' => 'F 3456 RTY', 'lokasi' => 'Summarecon Plaza', 'waktu' => '22 menit lalu', 'status' => 'masuk'],
                    ];
                @endphp

                @foreach ($activities as $item)
                    <div class="flex items-center justify-between py-3.5 {{ !$loop->last ? 'border-b border-[#E4E9EE]' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-[#F4F6F8] flex items-center justify-center text-[#5B6B7A]">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="7" width="18" height="10" rx="2"/>
                                    <circle cx="7.5" cy="17" r="1.5"/><circle cx="16.5" cy="17" r="1.5"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-[#0B1D33]">{{ $item['plat'] }}</p>
                                <p class="text-xs text-[#5B6B7A]">{{ $item['lokasi'] }} · {{ $item['waktu'] }}</p>
                            </div>
                        </div>

                        @if ($item['status'] === 'masuk')
                            <span class="text-xs font-semibold text-[#00C2A8] bg-[#00C2A8]/10 px-2.5 py-1 rounded-full">Masuk</span>
                        @elseif ($item['status'] === 'keluar')
                            <span class="text-xs font-semibold text-[#5B6B7A] bg-[#F4F6F8] px-2.5 py-1 rounded-full">Keluar</span>
                        @else
                            <span class="text-xs font-semibold text-red-500 bg-red-500/10 px-2.5 py-1 rounded-full">Gagal</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-[#0B1D33] rounded-2xl p-6 text-white">
            <h2 class="font-['Sora'] font-bold text-lg mb-1">Status Sistem</h2>
            <p class="text-sm text-white/60 mb-6">Semua kamera & server aktif normal</p>

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-white/70">Kamera Pengenalan</span>
                    <span class="flex items-center gap-1.5 text-sm font-medium text-[#00C2A8]">
                        <span class="w-2 h-2 rounded-full bg-[#00C2A8]"></span> Online
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-white/70">Server Verifikasi</span>
                    <span class="flex items-center gap-1.5 text-sm font-medium text-[#00C2A8]">
                        <span class="w-2 h-2 rounded-full bg-[#00C2A8]"></span> Online
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-white/70">Payment Gateway</span>
                    <span class="flex items-center gap-1.5 text-sm font-medium text-[#FFB100]">
                        <span class="w-2 h-2 rounded-full bg-[#FFB100]"></span> Lambat
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-white/70">Database</span>
                    <span class="flex items-center gap-1.5 text-sm font-medium text-[#00C2A8]">
                        <span class="w-2 h-2 rounded-full bg-[#00C2A8]"></span> Online
                    </span>
                </div>
            </div>

            <button class="w-full mt-6 bg-white/10 hover:bg-white/15 transition text-sm font-medium py-2.5 rounded-lg">
                Lihat Detail Sistem
            </button>
        </div>
    </div>

</x-layouts.dashboard>