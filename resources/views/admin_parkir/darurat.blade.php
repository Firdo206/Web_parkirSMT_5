@extends('layouts.parkir')

@section('title', 'Buka Pintu Darurat')
@section('heading', 'Buka Pintu Darurat')
@section('subheading', 'Buka palang secara manual. Setiap perintah tercatat')

@section('content')
<style>
    .dr-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.2fr);gap:20px;align-items:start}
    @media (max-width:900px){.dr-grid{grid-template-columns:1fr}}
    .dr-card h3{margin:0 0 14px;font-size:16px}
    .dr-state{display:flex;justify-content:space-between;gap:10px;padding:10px 12px;border-radius:10px;background:#f6f8fb;margin-bottom:8px;font-size:13.5px}
    .dr-state span{color:var(--muted)}
    .dr-warn{margin:0 0 16px;padding:10px 12px;border-radius:10px;background:#fff6e5;border:1px solid #f3dca8;font-size:13px;color:#7a5a12;line-height:1.5}
    .btn-danger{background:var(--red);color:#fff}
    .btn-danger:hover{filter:brightness(.94)}
    .dr-field textarea{width:100%;padding:9px 12px;border:1px solid var(--line);border-radius:8px;font:inherit;resize:vertical}
</style>

<div class="dr-grid">
    <div class="card dr-card">
        <h3>Buka palang</h3>

        <div class="dr-warn">
            Gunakan hanya bila kendaraan tertahan, sensor bermasalah, atau keadaan darurat.
            Perintah ini melewati verifikasi wajah dan plat.
        </div>

        @foreach ($gates as $key => $label)
            @php $st = $states[$key] ?? null; @endphp
            <div class="dr-state"><b>{{ $label }}</b><span>{{ $st->status ?? 'Belum ada data' }}</span></div>
        @endforeach

        <form method="POST" action="{{ route('parkir.darurat.open') }}" id="drForm" style="margin-top:16px">
            @csrf

            <div class="field">
                <label for="gate">Gerbang</label>
                <select id="gate" name="gate">
                    @foreach ($gates as $key => $label)
                        <option value="{{ $key }}" @selected(old('gate') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('gate') <div class="err">{{ $message }}</div> @enderror
            </div>

            <div class="field dr-field">
                <label for="reason">Alasan</label>
                <textarea id="reason" name="reason" rows="3" maxlength="255"
                          placeholder="Contoh: mobil member tertahan, sensor error">{{ old('reason') }}</textarea>
                @error('reason') <div class="err">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-danger" style="width:100%">Buka palang sekarang</button>
        </form>
    </div>

    <div class="card dr-card">
        <h3>Riwayat buka manual</h3>
        <table>
            <thead><tr><th>Waktu</th><th>Admin</th><th>Gerbang</th><th>Alasan</th></tr></thead>
            <tbody>
            @forelse ($logs as $l)
                <tr>
                    <td style="white-space:nowrap">{{ $l->created_at->format('d/m H:i') }}</td>
                    <td>{{ $l->admin->name ?? '-' }}</td>
                    <td>{{ $gates[$l->gate] ?? $l->gate }}</td>
                    <td>{{ $l->reason }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty">Belum ada riwayat.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('drForm').addEventListener('submit', e => {
        if (!confirm('Yakin buka palang sekarang? Verifikasi wajah dan plat akan dilewati.')) e.preventDefault();
    });
</script>
@endsection