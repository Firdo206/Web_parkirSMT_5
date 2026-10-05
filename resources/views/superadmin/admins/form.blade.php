@extends('layouts.superadmin')

@php $isEdit = $admin->exists; @endphp

@section('title', $isEdit ? 'Edit Admin' : 'Tambah Admin')
@section('heading', $isEdit ? 'Edit Akun Admin' : 'Tambah Akun Admin')
@section('subheading', $isEdit ? 'Perbarui data akun admin' : 'Buat akun baru untuk Admin Web atau Admin Parkir')

@section('content')
<div class="card" style="max-width:560px">
    <form method="POST"
          action="{{ $isEdit ? route('superadmin.admins.update', $admin) : route('superadmin.admins.store') }}">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="field">
            <label for="name">Nama</label>
            <input id="name" name="name" value="{{ old('name', $admin->name) }}" required>
            @error('name') <div class="err">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $admin->email) }}" required>
            @error('email') <div class="err">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="role">Role</label>
            <select id="role" name="role">
                @foreach (\App\Models\User::ADMIN_ROLES as $val => $label)
                    <option value="{{ $val }}" @selected(old('role', $admin->role) === $val)>{{ $label }}</option>
                @endforeach
            </select>
            @error('role') <div class="err">{{ $message }}</div> @enderror
        </div>

        @unless ($isEdit)
            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
                @error('password') <div class="err">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>
        @endunless

        <div style="display:flex;gap:8px;margin-top:8px">
            <button class="btn btn-primary">Simpan</button>
            <a href="{{ route('superadmin.admins.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection