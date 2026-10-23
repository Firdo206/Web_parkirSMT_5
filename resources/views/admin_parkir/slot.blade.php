@extends('layouts.parkir')

@section('title', 'Slot Parkir')
@section('heading', 'Slot Parkir')
@section('subheading', 'Atur kapasitas slot. Pendaftaran member baru ditolak kalau slot sudah penuh')

@section('content')
@php
    $s = $slots['mobil'];
    $persen = $s['kapasitas'] > 0
        ? min(100, (int) round($s['terisi'] / $s['kapasitas'] * 100))
        : ($s['terisi'] > 0 ? 100 : 0);
    $kelas = $persen >= 100 ? 'full' : ($persen >= 80 ? 'warn' : 'ok');
    $keliling = 2 * M_PI * 54;
    $offset = $keliling * (1 - $persen / 100);
@endphp

<style>
    .sl{--sl-ok:var(--green);--sl-warn:var(--amber);--sl-full:var(--red)}
    .sl-layout{display:grid;grid-template-columns:minmax(0,1.35fr) minmax(0,1fr);gap:22px;align-items:start}
    @media (max-width:960px){.sl-layout{grid-template-columns:1fr}}

    /* Kartu status */
    .sl-hero{padding:0;overflow:hidden}
    .sl-hero-top{display:flex;align-items:center;gap:28px;padding:28px;background:linear-gradient(135deg,var(--navy),var(--navy-2));color:#fff}
    @media (max-width:560px){.sl-hero-top{flex-direction:column;text-align:center}}
    .sl-ring{position:relative;width:150px;height:150px;flex:none}
    .sl-ring svg{width:100%;height:100%;transform:rotate(-90deg)}
    .sl-ring circle{fill:none;stroke-width:11;stroke-linecap:round}
    .sl-ring .track{stroke:rgba(255,255,255,.14)}
    .sl-ring .bar{transition:stroke-dashoffset .6s ease}
    .sl-ring.ok .bar{stroke:#34d399}
    .sl-ring.warn .bar{stroke:#fbbf24}
    .sl-ring.full .bar{stroke:#f87171}
    .sl-ring-text{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center}
    .sl-ring-text b{font-size:34px;line-height:1;font-weight:800}
    .sl-ring-text small{font-size:12px;color:#9fb3c8;margin-top:4px}
    .sl-title small{display:block;font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:#9fb3c8;margin-bottom:6px}
    .sl-title h2{margin:0;font-size:26px}
    .sl-title p{margin:8px 0 12px;font-size:14px;color:#c4d2e1;line-height:1.5}
    .sl-pill{display:inline-block;padding:5px 14px;border-radius:999px;font-size:12.5px;font-weight:700}
    .sl-pill.ok{background:#d1fae5;color:#065f46}
    .sl-pill.warn{background:#fef3c7;color:#92400e}
    .sl-pill.full{background:#fee2e2;color:#991b1b}

    .sl-stats{display:grid;grid-template-columns:repeat(4,1fr)}
    @media (max-width:640px){.sl-stats{grid-template-columns:repeat(2,1fr)}}
    .sl-stat{padding:20px 22px;border-right:1px solid var(--line)}
    .sl-stat:last-child{border-right:0}
    @media (max-width:640px){.sl-stat{border-bottom:1px solid var(--line)}}
    .sl-stat small{display:block;font-size:12px;color:var(--muted);margin-bottom:6px}
    .sl-stat b{font-size:26px;font-weight:800}
    .sl-stat span{display:block;font-size:11.5px;color:var(--muted);margin-top:2px}

    .sl-usage{padding:20px 24px;border-top:1px solid var(--line)}
    .sl-usage-head{display:flex;justify-content:space-between;font-size:13px;margin-bottom:8px}
    .sl-usage-head span{color:var(--muted)}
    .sl-bar{height:10px;border-radius:999px;background:#eef1f5;overflow:hidden}
    .sl-bar i{display:block;height:100%;border-radius:999px;transition:width .6s ease}
    .sl-bar i.ok{background:var(--sl-ok)}
    .sl-bar i.warn{background:var(--sl-warn)}
    .sl-bar i.full{background:var(--sl-full)}

    /* Kolom kanan */
    .sl-side{display:flex;flex-direction:column;gap:22px}
    .sl-card h3{margin:0 0 4px;font-size:16px}
    .sl-card .sub{margin:0 0 18px;font-size:13px;color:var(--muted)}
    .sl-stepper{display:flex;gap:8px}
    .sl-stepper input{text-align:center;font-size:20px;font-weight:700;height:48px}
    .sl-stepper button{width:48px;height:48px;flex:none;border:1px solid var(--line);border-radius:10px;background:#fff;font-size:22px;line-height:1;cursor:pointer;color:var(--text)}
    .sl-stepper button:hover{background:var(--bg);border-color:#c9d0d9}
    .sl-save{width:100%;margin-top:16px;height:44px}
    .sl-warn-box{margin-top:14px;padding:12px 14px;border-radius:10px;background:#fff6e5;border:1px solid #f3dca8;font-size:13px;color:#7a5a12;line-height:1.5}
    .sl-how{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:14px}
    .sl-how li{display:flex;gap:12px;font-size:13.5px;line-height:1.5;color:var(--muted)}
    .sl-how li i{flex:none;width:26px;height:26px;border-radius:50%;background:#e8f0fe;color:var(--accent);font-style:normal;font-weight:700;font-size:13px;display:grid;place-items:center}
    .sl-how b{color:var(--text)}
</style>

<form class="sl" method="POST" action="{{ route('parkir.slot.update') }}">
    @csrf
    @method('PUT')

    <div class="sl-layout">
        {{-- ============ KIRI: status ============ --}}
        <div class="card sl-hero">
            <div class="sl-hero-top">
                <div class="sl-ring {{ $kelas }}" role="img" aria-label="Terisi {{ $persen }} persen">
                    <svg viewBox="0 0 120 120">
                        <circle class="track" cx="60" cy="60" r="54"/>
                        <circle class="bar" cx="60" cy="60" r="54"
                                stroke-dasharray="{{ $keliling }}" stroke-dashoffset="{{ $offset }}"/>
                    </svg>
                    <div class="sl-ring-text"><b>{{ $persen }}%</b><small>terisi</small></div>
                </div>

                <div class="sl-title">
                    <small>Slot parkir</small>
                    <h2>Mobil</h2>
                    <p>
                        @if ($s['penuh'])
                            Slot sudah penuh. Pendaftaran member baru ditutup.
                        @else
                            Masih ada {{ $s['sisa'] }} slot yang bisa dipakai member baru.
                        @endif
                    </p>
                    <span class="sl-pill {{ $kelas }}">{{ $s['penuh'] ? 'Penuh' : ($kelas === 'warn' ? 'Hampir penuh' : 'Tersedia') }}</span>
                </div>
            </div>

            <div class="sl-stats">
                <div class="sl-stat"><small>Kapasitas</small><b>{{ $s['kapasitas'] }}</b><span>total slot</span></div>
                <div class="sl-stat"><small>Terisi</small><b>{{ $s['terisi'] }}</b><span>member aktif</span></div>
                <div class="sl-stat"><small>Sisa</small><b>{{ $s['sisa'] }}</b><span>bisa didaftarkan</span></div>
                <div class="sl-stat"><small>Di dalam</small><b>{{ $s['di_dalam'] }}</b><span>sekarang</span></div>
            </div>

            <div class="sl-usage">
                <div class="sl-usage-head"><b>Penggunaan slot</b><span>{{ $s['terisi'] }} dari {{ $s['kapasitas'] }}</span></div>
                <div class="sl-bar" role="progressbar" aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100">
                    <i class="{{ $kelas }}" style="width: {{ $persen }}%"></i>
                </div>
            </div>
        </div>

        {{-- ============ KANAN: form + catatan ============ --}}
        <div class="sl-side">
            <div class="card sl-card">
                <h3>Ubah kapasitas</h3>
                <p class="sub">Jumlah maksimal mobil member yang boleh terdaftar.</p>

                <div class="field" style="margin:0">
                    <label for="cap_mobil">Kapasitas mobil</label>
                    <div class="sl-stepper">
                        <button type="button" data-step="-1" aria-label="Kurangi">&minus;</button>
                        <input id="cap_mobil" type="number" name="capacity[mobil]" min="0" max="100000" inputmode="numeric"
                               value="{{ old('capacity.mobil', $s['kapasitas']) }}" required>
                        <button type="button" data-step="1" aria-label="Tambah">+</button>
                    </div>
                    @error('capacity.mobil') <div class="err">{{ $message }}</div> @enderror
                </div>

                @if ($s['kelebihan'] > 0)
                    <div class="sl-warn-box">
                        Terisi {{ $s['terisi'] }} melebihi kapasitas {{ $s['kapasitas'] }}. Member yang sudah ada tetap aktif,
                        tapi pendaftaran baru ditutup sampai ada langganan yang berakhir.
                    </div>
                @endif

                <button type="submit" class="btn btn-primary sl-save">Simpan kapasitas</button>
            </div>

            <div class="card sl-card">
                <h3>Cara slot dihitung</h3>
                <p class="sub">Supaya angka di halaman ini mudah dipahami.</p>
                <ul class="sl-how">
                    <li><i>1</i><span><b>Terisi</b> adalah kendaraan milik member aktif yang langganannya belum berakhir.</span></li>
                    <li><i>2</i><span>Begitu langganan habis, slot <b>kembali kosong</b> dengan sendirinya.</span></li>
                    <li><i>3</i><span><b>Di dalam</b> hanya informasi kendaraan yang sedang berada di area parkir, dan tidak membatasi pendaftaran.</span></li>
                </ul>
            </div>
        </div>
    </div>
</form>

<script>
    document.querySelectorAll('.sl-stepper button').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.parentElement.querySelector('input');
            const next = (parseInt(input.value, 10) || 0) + Number(btn.dataset.step);
            input.value = Math.min(Number(input.max) || 100000, Math.max(0, next));
        });
    });
</script>
@endsection