@extends('layouts.superadmin')

@section('title', 'Kelola Akun Admin')
@section('heading', 'Kelola Akun Admin')
@section('subheading', 'Buat dan atur akun Admin Web serta Admin Parkir')

@section('content')
<style>
    dialog.modal{border:0;border-radius:16px;padding:0;width:min(520px,92vw);box-shadow:0 20px 60px rgba(11,31,51,.35)}
    dialog.modal::backdrop{background:rgba(11,31,51,.55);backdrop-filter:blur(2px)}
    .modal-head{display:flex;justify-content:space-between;align-items:center;padding:20px 24px;border-bottom:1px solid var(--line)}
    .modal-head h2{margin:0;font-size:18px}
    .modal-x{background:none;border:0;font-size:24px;line-height:1;cursor:pointer;color:var(--muted)}
    .modal-body{padding:24px}
    .modal-foot{display:flex;justify-content:flex-end;gap:8px;padding:0 24px 24px}
</style>

<div class="toolbar">
    <form method="GET" class="filters">
        <input name="q" value="{{ request('q') }}" placeholder="Cari nama atau email">
        <select name="role">
            <option value="">Semua role</option>
            @foreach (\App\Models\User::ADMIN_ROLES as $val => $label)
                <option value="{{ $val }}" @selected(request('role') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn btn-dark">Filter</button>
    </form>
    <button type="button" class="btn btn-primary" onclick="openCreate()">+ Tambah Admin</button>
</div>

<div class="card" style="padding:8px 16px;overflow-x:auto">
    <table>
        <thead>
            <tr>
                <th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th style="text-align:right">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($admins as $a)
            <tr>
                <td><b>{{ $a->name }}</b></td>
                <td>{{ $a->email }}</td>
                <td><span class="badge b-role">{{ \App\Models\User::ADMIN_ROLES[$a->role] ?? $a->role }}</span></td>
                <td>
                    <span class="badge {{ $a->is_active ? 'b-on' : 'b-off' }}">
                        {{ $a->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td>
                    <div class="actions">
                        <button type="button" class="link l-edit"
                                data-id="{{ $a->id }}"
                                data-name="{{ $a->name }}"
                                data-email="{{ $a->email }}"
                                data-role="{{ $a->role }}"
                                onclick="openEdit(this)">Edit</button>

                        <form method="POST" action="{{ route('superadmin.admins.toggle', $a) }}">
                            @csrf @method('PATCH')
                            <button class="link l-warn">{{ $a->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                        </form>

                        <form method="POST" action="{{ route('superadmin.admins.reset', $a) }}"
                              onsubmit="return confirm('Reset password akun ini?')">
                            @csrf @method('PATCH')
                            <button class="link l-key">Reset Password</button>
                        </form>

                        <form method="POST" action="{{ route('superadmin.admins.destroy', $a) }}"
                              onsubmit="return confirm('Hapus akun ini?')">
                            @csrf @method('DELETE')
                            <button class="link l-del">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="empty">Belum ada akun admin. Klik "Tambah Admin" untuk membuat yang pertama.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px">{{ $admins->links() }}</div>

{{-- ===== MODAL TAMBAH / EDIT ===== --}}
<dialog id="adminModal" class="modal">
    <div class="modal-head">
        <h2 id="modalTitle">Tambah Akun Admin</h2>
        <button type="button" class="modal-x" onclick="closeModal()" aria-label="Tutup">&times;</button>
    </div>

    <form id="adminForm" method="POST">
        @csrf
        <input type="hidden" name="_method" id="fMethod" value="POST">
        <input type="hidden" name="_mode" id="fMode" value="create">
        <input type="hidden" name="_edit_id" id="fEditId" value="">

        <div class="modal-body">
            <div class="field">
                <label for="fName">Nama</label>
                <input id="fName" name="name" required>
                @error('name') <div class="err">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="fEmail">Email</label>
                <input id="fEmail" type="email" name="email" required>
                @error('email') <div class="err">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="fRole">Role</label>
                <select id="fRole" name="role">
                    @foreach (\App\Models\User::ADMIN_ROLES as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('role') <div class="err">{{ $message }}</div> @enderror
            </div>

            <div id="passwordFields">
                <div class="field">
                    <label for="fPassword">Password</label>
                    <input id="fPassword" type="password" name="password">
                    @error('password') <div class="err">{{ $message }}</div> @enderror
                </div>
                <div class="field">
                    <label for="fPasswordConfirm">Konfirmasi Password</label>
                    <input id="fPasswordConfirm" type="password" name="password_confirmation">
                </div>
            </div>
        </div>

        <div class="modal-foot">
            <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</dialog>

<script>
    const modal    = document.getElementById('adminModal');
    const form     = document.getElementById('adminForm');
    const storeUrl = @json(route('superadmin.admins.store'));
    const updateTpl = @json(route('superadmin.admins.update', 0)); // berakhir dengan /0

    function setMode(mode, id = null) {
        const isEdit = mode === 'edit';
        document.getElementById('modalTitle').textContent = isEdit ? 'Edit Akun Admin' : 'Tambah Akun Admin';
        document.getElementById('fMode').value   = mode;
        document.getElementById('fEditId').value = id ?? '';
        document.getElementById('fMethod').value = isEdit ? 'PUT' : 'POST';
        form.action = isEdit ? updateTpl.replace(/\/0$/, '/' + id) : storeUrl;

        // password hanya untuk akun baru
        const pw = document.getElementById('passwordFields');
        pw.style.display = isEdit ? 'none' : 'block';
        pw.querySelectorAll('input').forEach(i => { i.disabled = isEdit; i.required = !isEdit; i.value = ''; });
    }

    function fill(name, email, role) {
        document.getElementById('fName').value  = name ?? '';
        document.getElementById('fEmail').value = email ?? '';
        document.getElementById('fRole').value  = role || document.getElementById('fRole').options[0].value;
    }

    function openCreate() {
        setMode('create');
        fill('', '', '');
        modal.showModal();
    }

    function openEdit(btn) {
        setMode('edit', btn.dataset.id);
        fill(btn.dataset.name, btn.dataset.email, btn.dataset.role);
        modal.showModal();
    }

    function closeModal() { modal.close(); }

    // klik area gelap di luar kotak = tutup
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    // Kalau validasi gagal, buka lagi modalnya lengkap dengan isian & pesan error
    @if ($errors->any() && old('_mode'))
        setMode(@json(old('_mode')), @json(old('_edit_id')));
        fill(@json(old('name')), @json(old('email')), @json(old('role')));
        modal.showModal();
        @endif
        </script>
@endsection