<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ParkVisi — Parkir Tanpa Tiket, Cukup Wajah & Plat Nomor</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="landing-page">

<header>
  <nav>
    <a href="#" class="brand">
      <svg class="brand-mark" viewBox="0 0 28 28" fill="none"><rect width="28" height="28" rx="7" fill="#00C2A8"/><path d="M9 20V8h5.2a4 4 0 010 8H9" stroke="#0B1D33" stroke-width="2" stroke-linecap="round"/></svg>
      ParkVisi
    </a>
    <ul class="nav-links">
      <li><a href="#cara-kerja">Cara Kerja</a></li>
      <li><a href="#fitur">Fitur</a></li>
      <li><a href="#harga">Harga</a></li>
      <li><a href="#kontak">Kontak</a></li>
    </ul>
    <div class="nav-cta">
      <a href="#unduh" class="btn btn-amber">Unduh Aplikasi</a>
    </div>
    <button class="menu-toggle">☰</button>
  </nav>
</header>

<section class="hero" style="background-image:url('{{ asset('images/hero-parkir.jpg') }}')">
  <div class="wrap hero-content">
    <span class="eyebrow-line"><span class="dot"></span> Sistem parkir berbasis pengenalan wajah & plat nomor</span>
    <h1>Parkir Tanpa Tiket.<br><span class="h1-sub">Cukup Wajah dan Plat Nomor Anda.</span></h1>
    <p class="lead">ParkVisi menggantikan tiket dan kartu member dengan pengenalan wajah dan plat nomor otomatis — palang terbuka begitu Anda mendekat, tanpa berhenti, tanpa antre.</p>
    <div class="hero-actions">
      <a href="#unduh" class="btn btn-amber">Daftarkan Kendaraan Saya</a>
    </div>
  </div>
</section>

<section class="section" id="cara-kerja">
  <div class="wrap">
    <div class="section-head">
      <span class="tag">Cara kerja</span>
      <h2>Tiga langkah, sekali daftar, langsung bisa dipakai setiap kali Anda datang</h2>
      <p>Pendaftaran dilakukan sekali lewat aplikasi mobile. Setelah itu, sistem yang bekerja setiap kali Anda datang.</p>
    </div>
    <div class="steps">
      <div class="step">
        <div class="num">01</div>
        <h3>Daftarkan wajah & plat nomor</h3>
        <p>Ambil foto wajah dan masukkan nomor plat kendaraan lewat aplikasi ParkVisi. Data tersimpan aman dan terenkripsi.</p>
      </div>
      <div class="step">
        <div class="num">02</div>
        <h3>Kamera mengenali otomatis</h3>
        <p>Saat mendekati palang, kamera di titik masuk membaca wajah pengemudi dan plat nomor secara bersamaan untuk mencocokkan identitas.</p>
      </div>
      <div class="step">
        <div class="num">03</div>
        <h3>Palang terbuka, tarif tercatat</h3>
        <p>Setelah cocok, palang terbuka otomatis. Durasi parkir dan tarif dihitung sistem dan bisa dibayar langsung dari aplikasi.</p>
      </div>
    </div>
  </div>
</section>

<section class="split on-dark" id="fitur">
  <div class="wrap section">
    <div class="section-head">
      <span class="tag">Satu sistem, dua sisi</span>
      <h2>Dibangun terpisah untuk pengelola dan untuk pengguna kendaraan</h2>
      <p>Web admin mengatur operasional dari belakang layar, aplikasi mobile menjadi satu-satunya yang dibutuhkan pengguna saat parkir.</p>
    </div>
    <div class="split-grid">
      <div class="panel">
        <span class="sub">WEB ADMIN — untuk pengelola</span>
        <h3>Kendalikan operasional parkir dari satu dashboard</h3>
        <ul>
          <li><svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M4 9l3.5 3.5L14 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Pantau slot terisi dan kosong secara real-time per lantai</li>
          <li><svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M4 9l3.5 3.5L14 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Kelola daftar wajah & plat nomor terdaftar, blokir bila perlu</li>
          <li><svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M4 9l3.5 3.5L14 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Laporan pemasukan harian, mingguan, dan per titik masuk</li>
          <li><svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M4 9l3.5 3.5L14 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Riwayat kejadian saat pengenalan gagal, untuk verifikasi manual</li>
        </ul>
      </div>
      <div class="panel">
        <span class="sub">APLIKASI MOBILE — untuk pengguna</span>
        <h3>Cukup daftar sekali, lalu lupakan tiketnya</h3>
        <ul>
          <li><svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M4 9l3.5 3.5L14 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Pendaftaran wajah & plat nomor dalam hitungan menit</li>
          <li><svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M4 9l3.5 3.5L14 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Notifikasi saat masuk dan keluar area parkir</li>
          <li><svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M4 9l3.5 3.5L14 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Pembayaran langsung dari saldo aplikasi</li>
          <li><svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M4 9l3.5 3.5L14 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Lihat sisa slot parkir yang tersedia secara real-time</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="pricing-section" id="harga">
  <div class="wrap">
    <div class="section-head on-dark-head">
      <span class="tag">Harga</span>
      <h2>Pilih paket langganan sesuai kebutuhan Anda</h2>
      <p>Sekali daftar wajah & plat nomor, nikmati akses parkir tanpa tiket selama masa aktif paket.</p>
    </div>
    <div class="pricing-grid">
      @forelse ($plans as $plan)
        <div class="price-card {{ $plan->is_popular ? 'is-popular' : '' }}">
          @if ($plan->is_popular)
            <div class="price-card-header">★ Rekomendasi</div>
          @endif
          <div class="price-card-body">
            <h3 class="price-name">{{ $plan->name }}</h3>
            <div class="price-value">
              <span>Motor</span>
              <b>Rp {{ number_format($plan->price_motor, 0, ',', '.') }}</b>
              <small>/ {{ $plan->duration_label }}</small>
            </div>
            <div class="price-value">
              <span>Mobil</span>
              <b>Rp {{ number_format($plan->price_mobil, 0, ',', '.') }}</b>
              <small>/ {{ $plan->duration_label }}</small>
            </div>

            <a href="#unduh" class="btn {{ $plan->is_popular ? 'btn-amber' : 'btn-outline' }} price-cta">Pilih Paket</a>

            @if ($plan->description)
              <p class="price-desc">{{ $plan->description }}</p>
            @endif

            @if (count($plan->features_list))
              <ul class="price-features">
                @foreach ($plan->features_list as $feature)
                  <li>
                    <svg width="16" height="16" viewBox="0 0 18 18" fill="none"><path d="M4 9l3.5 3.5L14 6" stroke="#00C2A8" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ $feature }}
                  </li>
                @endforeach
              </ul>
            @endif
          </div>
        </div>
      @empty
        <p style="text-align:center;color:rgba(255,255,255,.6);">Paket harga belum tersedia saat ini.</p>
      @endforelse
    </div>
  </div>
</section>

<style>
  .pricing-section {
    background: #0B1D33;
    padding: 90px 0;
  }
  .pricing-section .on-dark-head h2,
  .pricing-section .on-dark-head p { color: #fff; }
  .pricing-section .on-dark-head p { color: rgba(255,255,255,.65); }
  .pricing-section .tag { color: #00C2A8; }

  .pricing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 26px;
    margin-top: 48px;
    align-items: start;
  }
  .price-card {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0,0,0,.18);
    transition: transform .2s ease;
  }
  .price-card:hover { transform: translateY(-6px); }
  .price-card.is-popular { transform: scale(1.04); box-shadow: 0 24px 48px rgba(0,0,0,.28); }
  .price-card.is-popular:hover { transform: scale(1.04) translateY(-6px); }

  .price-card-header {
    background: #00C2A8;
    color: #0B1D33;
    font-weight: 700;
    font-size: 13px;
    text-align: center;
    padding: 10px 0;
    letter-spacing: .02em;
  }
  .price-card-body { padding: 32px 26px 30px; }

  .price-name { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 20px; color: #0B1D33; margin-bottom: 18px; }

  .price-value { margin-bottom: 12px; }
  .price-value span { display: block; font-size: 12px; color: #5B6B7A; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 2px; }
  .price-value b { font-family: 'Sora', sans-serif; font-size: 26px; color: #0B1D33; }
  .price-value small { font-size: 12px; color: #5B6B7A; margin-left: 4px; }

  .price-cta { display: block; text-align: center; margin: 22px 0 18px; }
  .btn-outline {
    display: inline-block; padding: 12px 20px; border-radius: 10px;
    border: 2px solid #0B1D33; color: #0B1D33; font-weight: 700; font-size: 14px;
    transition: background .15s, color .15s;
  }
  .btn-outline:hover { background: #0B1D33; color: #fff; }

  .price-desc { color: #5B6B7A; font-size: 13px; margin-bottom: 16px; }

  .price-features { list-style: none; padding: 0; margin: 0; border-top: 1px solid #F0F2F4; padding-top: 18px; }
  .price-features li {
    display: flex; align-items: flex-start; gap: 10px;
    font-size: 14px; color: #0B1D33; padding: 7px 0;
  }
  .price-features svg { flex-shrink: 0; margin-top: 3px; }
</style>

<div class="band">
  <div class="wrap band-grid">
    <div><b>500+</b><span>slot parkir tersedia</span></div>
    <div><b>99,2%</b><span>akurasi pencocokan wajah & plat</span></div>
    <div><b>850+</b><span>kendaraan terdaftar</span></div>
    <div><b>&lt;2 dtk</b><span>waktu buka palang otomatis</span></div>
  </div>
</div>

<section class="section" id="unduh">
  <div class="wrap">
    <div class="cta">
      <div>
        <h2>Kelola parkir Anda, atau daftarkan kendaraan Anda hari ini.</h2>
        <p>Pengelola bisa mulai dari dashboard admin. Pengguna kendaraan cukup unduh aplikasinya.</p>
      </div>
      <div class="cta-actions">
        <a href="{{ route('login') }}" class="btn btn-amber">Coba Dashboard Admin</a>
        <a href="#" class="btn btn-ghost">Unduh Aplikasi ParkVisi</a>
      </div>
    </div>
  </div>
</section>

<footer id="kontak">
  <div class="wrap">
    <div class="foot-grid">
      <div>
        <a href="#" class="brand" style="color:#fff;margin-bottom:12px;display:inline-flex;">
          <svg class="brand-mark" viewBox="0 0 28 28" fill="none"><rect width="28" height="28" rx="7" fill="#00C2A8"/><path d="M9 20V8h5.2a4 4 0 010 8H9" stroke="#0B1D33" stroke-width="2" stroke-linecap="round"/></svg>
          ParkVisi
        </a>
        <p style="font-size:14px;max-width:260px;margin-top:12px;">Sistem manajemen parkir komersial dengan pengenalan wajah dan plat nomor otomatis.</p>
      </div>
      <div>
        <h4>Produk</h4>
        <ul>
          <li><a href="#cara-kerja">Cara Kerja</a></li>
          <li><a href="#fitur">Fitur</a></li>
          <li><a href="#">Dashboard Admin</a></li>
          <li><a href="#">Aplikasi Mobile</a></li>
        </ul>
      </div>
      <div>
        <h4>Perusahaan</h4>
        <ul>
          <li><a href="#">Tentang Kami</a></li>
          <li><a href="#">Karier</a></li>
        </ul>
      </div>
      <div>
        <h4>Kontak</h4>
        <ul>
          <li><a href="mailto:halo@parkvisi.id">halo@parkvisi.id</a></li>
          <li><a href="tel:+622100000000">(021) 0000-0000</a></li>
        </ul>
      </div>
    </div>
    <div class="foot-bottom">
      <span>© {{ date('Y') }} ParkVisi. Seluruh hak cipta dilindungi.</span>
      <span>Dibuat dengan Laravel</span>
    </div>
  </div>
</footer>

</body>
</html>