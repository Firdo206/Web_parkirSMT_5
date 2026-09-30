<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') — ParkVisi</title>
    <style>
        :root{--navy:#0b1f33;--navy2:#12304d;--teal:#14c8a8;--bg:#f4f6f8;--line:#e5e9ee;--text:#0f2236;--muted:#64748b;--red:#ef4444;--amber:#f59e0b}
        *{box-sizing:border-box}
        body{margin:0;font-family:system-ui,-apple-system,"Segoe UI",sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh}
        .sidebar{width:260px;background:var(--navy);color:#cbd5e1;display:flex;flex-direction:column;position:sticky;top:0;height:100vh;flex-shrink:0}
        .brand{display:flex;align-items:center;gap:12px;padding:24px;border-bottom:1px solid rgba(255,255,255,.08);color:#fff;font-weight:700;font-size:20px}
        .logo{width:36px;height:36px;border-radius:10px;background:var(--teal);color:var(--navy);display:grid;place-items:center;font-weight:800}
        .nav{padding:20px 16px;flex:1}
        .nav small{display:block;font-size:11px;letter-spacing:.08em;color:#64809b;margin:0 8px 10px;font-weight:600}
        .nav a,.nav button{display:block;width:100%;text-align:left;padding:11px 14px;border-radius:10px;color:#cbd5e1;text-decoration:none;font-size:15px;background:none;border:0;cursor:pointer;font-family:inherit;margin-bottom:4px}
        .nav a:hover,.nav button:hover{background:rgba(255,255,255,.06)}
        .nav a.active{background:var(--navy2);color:var(--teal)}
        .userbox{padding:18px 24px;border-top:1px solid rgba(255,255,255,.08);display:flex;gap:12px;align-items:center}
        .avatar{width:38px;height:38px;border-radius:50%;background:var(--teal);color:var(--navy);display:grid;place-items:center;font-weight:700}
        .userbox b{display:block;color:#fff;font-size:14px}
        .userbox span{font-size:12px;color:#8aa0b6}
        main{flex:1;min-width:0}
        .topbar{background:#fff;border-bottom:1px solid var(--line);padding:22px 32px}
        .topbar h1{margin:0;font-size:24px}
        .topbar p{margin:4px 0 0;color:var(--muted);font-size:14px}
        .content{padding:28px 32px}
        .card{background:#fff;border:1px solid var(--line);border-radius:16px;padding:24px}
        .btn{display:inline-block;padding:10px 18px;border-radius:10px;border:0;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;font-family:inherit}
        .btn-primary{background:var(--teal);color:var(--navy)}
        .btn-dark{background:var(--navy);color:#fff}
        .btn-outline{background:#fff;border:1px solid var(--line);color:var(--text)}
        .alert{padding:12px 16px;border-radius:10px;margin-bottom:18px;font-size:14px}
        .alert-ok{background:#d1fae5;color:#065f46}
        .alert-err{background:#fee2e2;color:#991b1b}
        table{width:100%;border-collapse:collapse;font-size:14px}
        th{text-align:left;color:var(--muted);font-weight:600;padding:12px;border-bottom:1px solid var(--line);font-size:13px}
        td{padding:14px 12px;border-bottom:1px solid var(--line);vertical-align:middle}
        tr:last-child td{border-bottom:0}
        .badge{display:inline-block;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600}
        .b-on{background:#d1fae5;color:#047857}.b-off{background:#fee2e2;color:#b91c1c}
        .b-role{background:#e0f2fe;color:#0369a1}
        .actions{display:flex;gap:6px;flex-wrap:wrap;justify-content:flex-end}
        .link{background:none;border:0;padding:6px 10px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;font-family:inherit}
        .l-edit{color:#0369a1}.l-warn{color:#b45309}.l-key{color:#6d28d9}.l-del{color:#dc2626}
        .link:hover{background:var(--bg)}
        label{display:block;font-size:14px;font-weight:600;margin-bottom:6px}
        input,select{width:100%;padding:11px 14px;border:1px solid var(--line);border-radius:10px;font-size:14px;font-family:inherit;background:#fff}
        input:focus,select:focus{outline:2px solid var(--teal);border-color:transparent}
        .field{margin-bottom:18px}.err{color:var(--red);font-size:13px;margin-top:4px}
        .toolbar{display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:18px}
        .filters{display:flex;gap:8px;flex-wrap:wrap}.filters input{width:240px}.filters select{width:auto}
        .empty{text-align:center;color:var(--muted);padding:32px}
        @media(max-width:800px){.sidebar{display:none}.content,.topbar{padding:20px}}
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="brand"><div class="logo">P</div> ParkVisi</div>
        <nav class="nav">
            <small>SUPER ADMIN</small>
            <a href="{{ route('superadmin.admins.index') }}"
               class="{{ request()->routeIs('superadmin.admins.*') ? 'active' : '' }}">Kelola Akun Admin</a>
            <small style="margin-top:22px">AKUN</small>
            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">Profil Saya</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Keluar</button>
            </form>
        </nav>
        <div class="userbox">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div><b>{{ auth()->user()->name }}</b><span>{{ auth()->user()->email }}</span></div>
        </div>
    </aside>

    <main>
        <div class="topbar">
            <h1>@yield('heading')</h1>
            <p>@yield('subheading')</p>
        </div>
        <div class="content">
            @if (session('success'))
                <div class="alert alert-ok">{{ session('success') }}</div>
            @endif
            @if ($errors->any() && ! request()->isMethod('get') === false)
            @endif
            @yield('content')
        </div>
    </main>
</body>
</html>