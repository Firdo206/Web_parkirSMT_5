@extends('layouts.parkir')

@section('title', 'Data Pelanggan')
@section('heading', 'Data Pelanggan')
@section('subheading', 'Daftar pelanggan yang sudah terdaftar wajah dan plat nomornya')

@section('content')

    {{-- RINGKASAN --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:16px">
        <div class="card">
            <div style="font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.04em">Total Pelanggan</div>
            <div style="font-size:26px;font-weight:700;margin-top:4px">{{ $stats['total'] }}</div>
        </div>
        <div class="card">
            <div style="font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.04em">Aktif</div>
            <div style="font-size:26px;font-weight:700;margin-top:4px;color:var(--green)">{{ $stats['aktif'] }}</div>
        </div>
        <div class="card">
            <div style="font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.04em">Nonaktif</div>
            <div style="font-size:26px;font-weight:700;margin-top:4px;color:var(--red)">{{ $stats['nonaktif'] }}</div>
        </div>
    </div>

    <div class="card">

        {{-- TOOLBAR: cari + filter + tombol tambah --}}
        <div class="toolbar">
            <form method="GET" action="{{ route('parkir.pelanggan') }}" class="filters">
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama, HP, atau plat nomor..." style="min-width:260px">
                <select name="status">
                    <option value="">Semua status</option>
                    <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
                </select>
                <button type="submit" class="btn btn-dark">Cari</button>
                @if ($search !== '' || $status !== '')
                    <a href="{{ route('parkir.pelanggan') }}" class="btn btn-outline" style="text-decoration:none">Reset</a>
                @endif
            </form>

            <a href="{{ route('parkir.pendaftaran') }}" class="btn btn-primary" style="text-decoration:none">+ Daftarkan Pelanggan</a>
        </div>

        {{-- TABEL --}}
        <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th>Pelanggan</th>
                        <th>Kontak</th>
                        <th>Kendaraan</th>
                        <th>Paket</th>
                        <th>Wajah</th>
                        <th>Status</th>
                        <th>Terdaftar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($members as $member)
                        <tr>
                            {{-- Nama + foto --}}
                            <td>
                                <div style="display:flex;align-items:center;gap:12px">
                                    @if ($member->faceProfile && $member->faceProfile->photo_path)
                                        <img src="{{ asset('storage/' . $member->faceProfile->photo_path) }}"
                                             alt="Foto {{ $member->name }}"
                                             style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:1px solid var(--line)">
                                    @else
                                        <div style="width:40px;height:40px;border-radius:50%;background:var(--navy-2);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div style="font-weight:600">{{ $member->name }}</div>
                                        <div style="font-size:12px;color:var(--muted)">ID #{{ $member->id }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Kontak --}}
                            <td>
                                <div>{{ $member->phone ?: '-' }}</div>
                                <div style="font-size:12px;color:var(--muted)">{{ $member->email ?: '-' }}</div>
                            </td>

                            {{-- Kendaraan (bisa lebih dari satu) --}}
                            <td>
                                @forelse ($member->vehicles as $vehicle)
                                    <div style="margin-bottom:4px">
                                        <span class="badge b-role">{{ $vehicle->plate_number }}</span>
                                        <span style="font-size:12px;color:var(--muted);margin-left:4px">{{ ucfirst($vehicle->vehicle_type) }}</span>
                                    </div>
                                @empty
                                    <span style="color:var(--muted)">-</span>
                                @endforelse
                            </td>

                            {{-- Paket langganan terbaru --}}
                            <td>
                                @php $sub = $member->latestSubscription; @endphp
                                @if ($sub)
                                    <div style="font-weight:600;margin-bottom:4px">{{ $sub->plan?->name ?? '-' }}</div>
                                    @if ($sub->isActive())
                                        <span class="badge b-on">Aktif</span>
                                        <div style="font-size:12px;color:var(--muted);margin-top:4px">
                                            s.d. {{ $sub->end_date->format('d M Y') }}
                                            · sisa {{ (int) now()->startOfDay()->diffInDays($sub->end_date) }} hari
                                        </div>
                                    @elseif ($sub->end_date->lt(now()->startOfDay()))
                                        <span class="badge b-off">Kedaluwarsa</span>
                                        <div style="font-size:12px;color:var(--muted);margin-top:4px">
                                            berakhir {{ $sub->end_date->format('d M Y') }}
                                        </div>
                                    @else
                                        <span class="badge b-off">{{ ucfirst($sub->status) }}</span>
                                    @endif
                                @else
                                    <span style="color:var(--muted)">Belum berlangganan</span>
                                @endif
                            </td>

                            {{-- Status wajah --}}
                            <td>
                                @if ($member->faceProfile)
                                    <span class="badge b-on">Terdaftar</span>
                                @else
                                    <span class="badge b-off">Belum ada</span>
                                @endif
                            </td>

                            {{-- Status member --}}
                            <td>
                                @if ($member->is_active)
                                    <span class="badge b-on">Aktif</span>
                                @else
                                    <span class="badge b-off">Nonaktif</span>
                                @endif
                            </td>

                            <td style="color:var(--muted);font-size:13px">
                                {{ $member->created_at?->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty">
                                @if ($search !== '' || $status !== '')
                                    Tidak ada pelanggan yang cocok dengan pencarian.
                                @else
                                    Belum ada pelanggan terdaftar.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION (dibuat manual karena layout ini tidak pakai Tailwind) --}}
        @if ($members->hasPages())
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:16px;font-size:13px;color:var(--muted)">
                <span>
                    Menampilkan {{ $members->firstItem() }}–{{ $members->lastItem() }} dari {{ $members->total() }}
                </span>
                <div style="display:flex;gap:8px">
                    @if ($members->onFirstPage())
                        <span class="btn btn-outline" style="opacity:.45;cursor:default">← Sebelumnya</span>
                    @else
                        <a href="{{ $members->previousPageUrl() }}" class="btn btn-outline" style="text-decoration:none">← Sebelumnya</a>
                    @endif

                    @if ($members->hasMorePages())
                        <a href="{{ $members->nextPageUrl() }}" class="btn btn-outline" style="text-decoration:none">Berikutnya →</a>
                    @else
                        <span class="btn btn-outline" style="opacity:.45;cursor:default">Berikutnya →</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

@endsection