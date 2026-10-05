<x-layouts.dashboard title="Dashboard" subtitle="Ringkasan konten website & paket harga">

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">

        <div class="bg-white rounded-2xl border border-[#E4E9EE] p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-[#00C2A8]/10 flex items-center justify-center text-[#00C2A8]">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="16" rx="2"/>
                        <path d="M3 9h18M8 4v5"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-[#0B1D33] bg-[#F4F6F8] px-2 py-1 rounded-full">Aktif</span>
            </div>
            <p class="text-2xl font-['Sora'] font-bold text-[#0B1D33]">4</p>
            <p class="text-sm text-[#5B6B7A] mt-1">Paket Harga Ditawarkan</p>
        </div>

        <div class="bg-white rounded-2xl border border-[#E4E9EE] p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-[#5B6B7A]/10 flex items-center justify-center text-[#5B6B7A]">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 20h9M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-['Sora'] font-bold text-[#0B1D33]">3 hari lalu</p>
            <p class="text-sm text-[#5B6B7A] mt-1">Terakhir Konten Website Diubah</p>
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
                        ['judul' => 'Bagian Hero landing page diperbarui', 'detail' => 'Foto & headline diganti', 'waktu' => '3 hari lalu', 'tipe' => 'konten'],
                        ['judul' => 'Paket "Pro" harganya diubah', 'detail' => 'Rp89.000 → Rp99.000 / bulan', 'waktu' => '5 hari lalu', 'tipe' => 'paket'],
                        ['judul' => 'Bagian Fitur di landing page ditambahkan', 'detail' => 'Bagian "Keamanan" ditambahkan', 'waktu' => '2 minggu lalu', 'tipe' => 'konten'],
                        ['judul' => 'Paket "Enterprise" ditambahkan', 'detail' => 'Rp249.000 / bulan', 'waktu' => '3 minggu lalu', 'tipe' => 'paket'],
                    ];
                @endphp

                @foreach ($activities as $item)
                    <div class="flex items-center justify-between py-3.5 {{ !$loop->last ? 'border-b border-[#E4E9EE]' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-[#F4F6F8] flex items-center justify-center text-[#5B6B7A]">
                                @if ($item['tipe'] === 'konten')
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M8 4v5"/></svg>
                                @else
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="6" width="16" height="12" rx="2"/><path d="M4 10h16"/></svg>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-[#0B1D33]">{{ $item['judul'] }}</p>
                                <p class="text-xs text-[#5B6B7A]">{{ $item['detail'] }} · {{ $item['waktu'] }}</p>
                            </div>
                        </div>

                        @if ($item['tipe'] === 'konten')
                            <span class="text-xs font-semibold text-[#5B6B7A] bg-[#F4F6F8] px-2.5 py-1 rounded-full">Konten</span>
                        @else
                            <span class="text-xs font-semibold text-[#FFB100] bg-[#FFB100]/10 px-2.5 py-1 rounded-full">Paket Harga</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-[#E4E9EE] p-6">
            <h2 class="font-['Sora'] font-bold text-base text-[#0B1D33] mb-1">Konten Website</h2>
            <p class="text-sm text-[#5B6B7A] mb-5">Landing page saat ini sudah tayang</p>
            <a href="#" class="block text-center w-full bg-[#F4F6F8] hover:bg-[#E4E9EE] transition text-sm font-medium text-[#0B1D33] py-2.5 rounded-lg mb-3">
                Edit Konten Landing Page
            </a>
            <a href="#" class="block text-center w-full bg-[#0B1D33] hover:bg-[#132A47] transition text-sm font-medium text-white py-2.5 rounded-lg">
                Kelola Paket Harga
            </a>
        </div>
    </div>

</x-layouts.dashboard>