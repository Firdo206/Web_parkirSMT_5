@extends('layouts.parkir')

@section('title', 'Pendaftaran Member')
@section('heading', 'Pendaftaran Member')
@section('subheading', 'Daftarkan member baru beserta plat nomor dan wajah')

@section('content')
<style>
    .pd{
        --pd-line: var(--line, #e3e7ec);
        --pd-muted: var(--muted, #6b7685);
        --pd-accent: var(--accent, #2563eb);
        --pd-red: var(--red, #d64545);
        --pd-soft: #f5f7fa;
        --pd-radius: 14px;
    }
    .pd *{box-sizing:border-box}

    /* ---------- Layout ---------- */
    .pd-grid{display:grid;grid-template-columns:minmax(0,1.15fr) minmax(0,1fr);gap:24px;align-items:start}
    @media (max-width:960px){.pd-grid{grid-template-columns:1fr}}
    .pd-side{position:sticky;top:20px}
    @media (max-width:960px){.pd-side{position:static}}

    .pd-panel{background:#fff;border:1px solid var(--pd-line);border-radius:var(--pd-radius);padding:24px}
    .pd-panel + .pd-panel{margin-top:20px}
    .pd-head{margin:0 0 18px}
    .pd-head h3{margin:0;font-size:16px;font-weight:700}
    .pd-head p{margin:4px 0 0;font-size:13px;color:var(--pd-muted)}

    /* ---------- Form fields ---------- */
    .pd-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    @media (max-width:560px){.pd-row{grid-template-columns:1fr}}
    .pd .field{margin:0 0 14px}
    .pd .field:last-child{margin-bottom:0}
    .pd .field label{display:block;font-size:13px;font-weight:600;margin-bottom:6px}
    .pd .field .opt{font-weight:400;color:var(--pd-muted)}
    .pd .field input[type=text],
    .pd .field input[type=email],
    .pd .field input:not([type]),
    .pd .field select,
    .pd-plate input,
    .pd-plate select{
        width:100%;height:42px;padding:0 12px;border:1px solid var(--pd-line);border-radius:10px;
        background:#fff;font:inherit;font-size:14px;color:inherit;transition:border-color .15s, box-shadow .15s
    }
    .pd .field input:focus, .pd-plate input:focus, .pd-plate select:focus{
        outline:none;border-color:var(--pd-accent);box-shadow:0 0 0 3px color-mix(in srgb, var(--pd-accent) 18%, transparent)
    }
    .pd .err{color:var(--pd-red);font-size:12.5px;margin-top:6px}

    /* ---------- Plat nomor ---------- */
    .pd-plates{display:flex;flex-direction:column;gap:10px}
    .pd-plate{display:grid;grid-template-columns:minmax(0,1fr) 120px 38px;gap:8px;align-items:center}
    .pd-plate input{text-transform:uppercase;letter-spacing:.06em;font-weight:600}
    .pd-plate .pd-del{
        width:38px;height:42px;border:1px solid var(--pd-line);border-radius:10px;background:#fff;
        color:var(--pd-muted);font-size:18px;line-height:1;cursor:pointer;transition:all .15s
    }
    .pd-plate .pd-del:hover{background:#fdeeee;border-color:#f3c4c4;color:var(--pd-red)}
    .pd-plate .pd-del.is-hidden{visibility:hidden}
    .pd-add{
        margin-top:10px;display:inline-flex;align-items:center;gap:6px;background:none;border:0;padding:6px 2px;
        color:var(--pd-accent);font:inherit;font-size:13.5px;font-weight:600;cursor:pointer
    }
    .pd-add:hover{text-decoration:underline}

    /* ---------- Panggung foto ---------- */
    .pd-stage{
        position:relative;aspect-ratio:4/3;width:100%;border-radius:var(--pd-radius);overflow:hidden;
        background:var(--pd-soft);border:2px dashed var(--pd-line);display:flex;align-items:center;justify-content:center;
        transition:border-color .15s, background .15s
    }
    .pd-stage.is-drag{border-color:var(--pd-accent);background:color-mix(in srgb, var(--pd-accent) 6%, #fff)}
    .pd-stage.is-filled,.pd-stage.is-live{border-style:solid;border-color:transparent;background:#0e1116}
    .pd-stage.has-error{border-color:var(--pd-red)}

    .pd-empty{text-align:center;padding:20px;color:var(--pd-muted)}
    .pd-empty svg{width:44px;height:44px;margin-bottom:10px;stroke:currentColor;opacity:.7}
    .pd-empty strong{display:block;color:#1f2733;font-size:14px;margin-bottom:4px}
    .pd-empty span{font-size:12.5px}

    .pd-video,.pd-shot{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:none}
    .pd-video{transform:scaleX(-1)} /* cermin hanya untuk tampilan, hasil foto tidak ter-mirror */
    .pd-stage.is-live .pd-video{display:block}
    .pd-stage.is-filled .pd-shot{display:block}
    .pd-shot.is-mirror{transform:scaleX(-1)} /* hanya preview foto dari kamera; file yang dikirim tetap tidak ter-mirror */
    .pd-stage.is-live .pd-empty,.pd-stage.is-filled .pd-empty{display:none}

    /* Panduan posisi wajah */
    .pd-guide{position:absolute;inset:0;display:none;align-items:center;justify-content:center;pointer-events:none}
    .pd-stage.is-live .pd-guide{display:flex}
    .pd-guide i{
        width:46%;aspect-ratio:3/4;border-radius:50%;border:2px solid rgba(255,255,255,.85);
        box-shadow:0 0 0 999px rgba(0,0,0,.35)
    }
    .pd-guide em{
        position:absolute;bottom:12px;left:0;right:0;text-align:center;font-style:normal;
        font-size:12.5px;color:#fff;text-shadow:0 1px 3px rgba(0,0,0,.6)
    }

    /* Tombol di bawah panggung */
    .pd-actions{display:flex;gap:10px;margin-top:14px;flex-wrap:wrap}
    .pd-btn{
        flex:1;min-width:130px;height:42px;display:inline-flex;align-items:center;justify-content:center;gap:8px;
        border-radius:10px;border:1px solid var(--pd-line);background:#fff;color:inherit;font:inherit;font-size:14px;font-weight:600;
        cursor:pointer;transition:all .15s
    }
    .pd-btn:hover{border-color:#c9d0d9;background:var(--pd-soft)}
    .pd-btn.primary{background:var(--pd-accent);border-color:var(--pd-accent);color:#fff}
    .pd-btn.primary:hover{filter:brightness(.94)}
    .pd-btn[hidden]{display:none}
    .pd-btn svg{width:18px;height:18px;stroke:currentColor}

    .pd-msg{margin-top:12px;font-size:12.5px;color:var(--pd-muted)}
    .pd-msg.is-error{color:var(--pd-red)}
    .pd-tips{margin:16px 0 0;padding:12px 14px;border-radius:10px;background:var(--pd-soft);font-size:12.5px;color:var(--pd-muted);line-height:1.55}

    /* input file disembunyikan tapi tetap ada di form */
    .pd-file{position:absolute;width:1px;height:1px;opacity:0;pointer-events:none}

    /* ---------- Footer ---------- */
    .pd-foot{display:flex;justify-content:flex-end;gap:10px;margin-top:24px}
    .pd-foot .btn{min-width:130px;justify-content:center}
</style>

<form class="pd" id="pdForm" method="POST" action="{{ route('parkir.pendaftaran.store') }}" enctype="multipart/form-data" novalidate>
    @csrf

    <div class="pd-grid">
        {{-- ================= KIRI: data member & kendaraan ================= --}}
        <div>
            <section class="pd-panel">
                <div class="pd-head">
                    <h3>Data member</h3>
                    <p>Informasi dasar untuk identitas member.</p>
                </div>

                <div class="field">
                    <label for="name">Nama lengkap</label>
                    <input id="name" name="name" value="{{ old('name') }}" autocomplete="off" required>
                    @error('name') <div class="err">{{ $message }}</div> @enderror
                </div>

                <div class="pd-row">
                    <div class="field">
                        <label for="phone">No. HP</label>
                        <input id="phone" name="phone" value="{{ old('phone') }}" inputmode="tel" autocomplete="off">
                        @error('phone') <div class="err">{{ $message }}</div> @enderror
                    </div>
                    <div class="field">
                        <label for="email">Email <span class="opt">(opsional)</span></label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="off">
                        @error('email') <div class="err">{{ $message }}</div> @enderror
                    </div>
                </div>
            </section>

            <section class="pd-panel">
                <div class="pd-head">
                    <h3>Kendaraan</h3>
                    <p>Satu member bisa memiliki lebih dari satu kendaraan.</p>
                </div>

                <div class="pd-plates" id="plateList">
                    <div class="pd-plate">
                        <input type="text" name="plates[]" placeholder="B 1234 ABC" autocomplete="off" required>
                        <select name="vehicle_types[]" aria-label="Jenis kendaraan">
                            <option value="mobil">Mobil</option>
                            <option value="motor">Motor</option>
                        </select>
                        <button type="button" class="pd-del is-hidden" aria-label="Hapus plat">&times;</button>
                    </div>
                </div>

                <button type="button" class="pd-add" id="btnAddPlate">+ Tambah kendaraan</button>
                @error('plates.*') <div class="err">{{ $message }}</div> @enderror
                @error('plates') <div class="err">{{ $message }}</div> @enderror
            </section>
        </div>

        {{-- ================= KANAN: foto wajah ================= --}}
        <aside class="pd-side">
            <section class="pd-panel">
                <div class="pd-head">
                    <h3>Foto wajah</h3>
                    <p>Dipakai untuk mengenali member di gerbang.</p>
                </div>

                <div class="pd-stage" id="stage">
                    {{-- Kondisi kosong --}}
                    <div class="pd-empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="9" r="3.6"/><path d="M4.5 20c.9-3.6 3.9-5.6 7.5-5.6s6.6 2 7.5 5.6"/>
                        </svg>
                        <strong>Belum ada foto</strong>
                        <span>Buka kamera atau pilih foto dari komputer</span>
                    </div>

                    {{-- Kondisi kamera menyala --}}
                    <video class="pd-video" id="camVideo" autoplay playsinline muted></video>
                    <div class="pd-guide"><i></i><em>Posisikan wajah di dalam bingkai</em></div>

                    {{-- Kondisi foto sudah ada --}}
                    <img class="pd-shot" id="shot" alt="Foto wajah member">
                </div>
                <canvas id="camCanvas" hidden></canvas>

                <div class="pd-actions">
                    <button type="button" class="pd-btn primary" id="btnOpen">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h3l1.5-2h7L17 8h3v11H4z"/><circle cx="12" cy="13" r="3.2"/></svg>
                        Buka kamera
                    </button>
                    <button type="button" class="pd-btn primary" id="btnSnap" hidden>Ambil foto</button>
                    <button type="button" class="pd-btn" id="btnCancel" hidden>Batal</button>
                    <button type="button" class="pd-btn" id="btnRetake" hidden>Ulangi foto</button>
                    <button type="button" class="pd-btn" id="btnUpload">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16V5m0 0-4 4m4-4 4 4M5 19h14"/></svg>
                        Pilih file
                    </button>
                </div>

                <input class="pd-file" type="file" name="photo" id="photoInput" accept="image/*" tabindex="-1">

                <div class="pd-msg" id="msg" role="status"></div>
                @error('photo') <div class="err">{{ $message }}</div> @enderror

                <div class="pd-tips">
                    Pastikan pencahayaan cukup, wajah menghadap lurus ke kamera, dan hanya ada satu orang dalam foto.
                </div>
            </section>
        </aside>
    </div>

    <div class="pd-foot">
        <a href="{{ route('parkir.dashboard') }}" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan member</button>
    </div>
</form>

<script>
(function () {
    // ================= Plat nomor =================
    const plateList = document.getElementById('plateList');

    function syncDeleteButtons() {
        const rows = plateList.querySelectorAll('.pd-plate');
        rows.forEach(r => r.querySelector('.pd-del').classList.toggle('is-hidden', rows.length < 2));
    }

    document.getElementById('btnAddPlate').addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'pd-plate';
        row.innerHTML = `
            <input type="text" name="plates[]" placeholder="B 1234 ABC" autocomplete="off">
            <select name="vehicle_types[]" aria-label="Jenis kendaraan">
                <option value="mobil">Mobil</option>
                <option value="motor">Motor</option>
            </select>
            <button type="button" class="pd-del" aria-label="Hapus plat">&times;</button>`;
        plateList.appendChild(row);
        syncDeleteButtons();
        row.querySelector('input').focus();
    });

    plateList.addEventListener('click', e => {
        const btn = e.target.closest('.pd-del');
        if (!btn) return;
        btn.closest('.pd-plate').remove();
        syncDeleteButtons();
    });

    // ================= Foto wajah =================
    const stage    = document.getElementById('stage');
    const video    = document.getElementById('camVideo');
    const canvas   = document.getElementById('camCanvas');
    const shot     = document.getElementById('shot');
    const input    = document.getElementById('photoInput');
    const msg      = document.getElementById('msg');
    const btnOpen  = document.getElementById('btnOpen');
    const btnSnap  = document.getElementById('btnSnap');
    const btnCancel= document.getElementById('btnCancel');
    const btnRetake= document.getElementById('btnRetake');
    const btnUpload= document.getElementById('btnUpload');
    let stream = null;

    function setMsg(text, isError) {
        msg.textContent = text || '';
        msg.classList.toggle('is-error', !!isError);
        stage.classList.toggle('has-error', !!isError);
    }

    // state: 'empty' | 'live' | 'filled'
    function setState(state) {
        stage.classList.toggle('is-live',   state === 'live');
        stage.classList.toggle('is-filled', state === 'filled');
        btnOpen.hidden   = state !== 'empty';
        btnUpload.hidden = state === 'live';
        btnSnap.hidden   = state !== 'live';
        btnCancel.hidden = state !== 'live';
        btnRetake.hidden = state !== 'filled';
    }

    function showFile(file, mirror) {
        const reader = new FileReader();
        reader.onload = e => {
            shot.classList.toggle('is-mirror', !!mirror);
            shot.src = e.target.result;
            setState('filled');
            setMsg(file.name);
        };
        reader.readAsDataURL(file);
    }

    function stopCam() {
        if (stream) stream.getTracks().forEach(t => t.stop());
        stream = null;
        video.srcObject = null;
    }

    async function openCam() {
        setMsg('');
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            setMsg('Kamera tidak tersedia. Buka halaman ini lewat localhost atau HTTPS.', true);
            return;
        }
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } },
                audio: false
            });
        } catch (e) {
            const pesan = {
                NotAllowedError: 'Izin kamera ditolak. Klik ikon gembok di address bar lalu izinkan kamera.',
                NotFoundError: 'Kamera tidak ditemukan. Pastikan webcam terpasang.',
                NotReadableError: 'Kamera sedang dipakai aplikasi lain. Tutup aplikasi itu lalu coba lagi.'
            };
            setMsg(pesan[e.name] || ('Kamera tidak bisa diakses: ' + e.message), true);
            return;
        }
        video.srcObject = stream;
        setState('live');
    }

    function snap() {
        if (!video.videoWidth) {
            setMsg('Kamera belum siap, tunggu sebentar lalu coba lagi.', true);
            return;
        }
        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        canvas.toBlob(blob => {
            const file = new File([blob], 'capture.jpg', { type: 'image/jpeg' });
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            stopCam();
            showFile(file, true);
            setMsg('Foto dari kamera');
        }, 'image/jpeg', 0.92);
    }

    function reset() {
        stopCam();
        input.value = '';
        shot.removeAttribute('src');
        setState('empty');
        setMsg('');
    }

    btnOpen.addEventListener('click', openCam);
    btnSnap.addEventListener('click', snap);
    btnCancel.addEventListener('click', reset);
    btnRetake.addEventListener('click', () => { reset(); openCam(); });
    btnUpload.addEventListener('click', () => input.click());

    input.addEventListener('change', () => {
        if (input.files[0]) { stopCam(); showFile(input.files[0]); }
    });

    // Drag & drop ke panggung
    ['dragenter', 'dragover'].forEach(ev => stage.addEventListener(ev, e => {
        e.preventDefault(); stage.classList.add('is-drag');
    }));
    ['dragleave', 'drop'].forEach(ev => stage.addEventListener(ev, e => {
        e.preventDefault(); stage.classList.remove('is-drag');
    }));
    stage.addEventListener('drop', e => {
        const file = e.dataTransfer.files[0];
        if (!file || !file.type.startsWith('image/')) {
            setMsg('File harus berupa gambar (JPG/PNG).', true);
            return;
        }
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        stopCam();
        showFile(file);
    });

    window.addEventListener('beforeunload', stopCam);

    // ================= Submit =================
    document.getElementById('pdForm').addEventListener('submit', e => {
        // Buang baris plat tambahan yang kosong supaya tidak terkirim sebagai data kosong
        const rows = plateList.querySelectorAll('.pd-plate');
        rows.forEach((row, i) => {
            if (i > 0 && !row.querySelector('input').value.trim()) row.remove();
        });

        const firstPlate = plateList.querySelector('input');
        if (!document.getElementById('name').value.trim()) {
            e.preventDefault(); document.getElementById('name').focus(); return;
        }
        if (!firstPlate.value.trim()) {
            e.preventDefault(); firstPlate.focus(); return;
        }
        if (!input.files.length) {
            e.preventDefault();
            setMsg('Foto wajah wajib diisi. Ambil dari kamera atau pilih file.', true);
            stage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    setState('empty');
})();
</script>
@endsection