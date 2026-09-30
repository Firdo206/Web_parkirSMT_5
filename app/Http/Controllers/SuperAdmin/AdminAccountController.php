<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminAccountController extends Controller
{
    private function adminRoles(): array
    {
        return array_keys(User::ADMIN_ROLES);
    }

    public function index(Request $request)
    {
        $admins = User::whereIn('role', $this->adminRoles())
            ->when($request->q, fn ($q, $s) =>
                $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")))
            ->when($request->role, fn ($q, $r) => $q->where('role', $r))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('superadmin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('superadmin.admins.form', ['admin' => new User()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'role'     => ['required', Rule::in($this->adminRoles())],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->role = $data['role'];
        $user->is_active = true;
        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('superadmin.admins.index')
            ->with('success', 'Akun admin berhasil dibuat.');
    }

    public function edit(User $admin)
    {
        $this->ensureAdmin($admin);

        return view('superadmin.admins.form', compact('admin'));
    }

    public function update(Request $request, User $admin)
    {
        $this->ensureAdmin($admin);

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($admin->id)],
            'role'  => ['required', Rule::in($this->adminRoles())],
        ]);

        $admin->name = $data['name'];
        $admin->email = $data['email'];
        $admin->role = $data['role'];
        $admin->save();

        return redirect()->route('superadmin.admins.index')
            ->with('success', 'Akun admin diperbarui.');
    }

    public function toggle(User $admin)
    {
        $this->ensureAdmin($admin);

        $admin->is_active = ! $admin->is_active;
        $admin->save();

        return back()->with('success', $admin->is_active ? 'Akun diaktifkan.' : 'Akun dinonaktifkan.');
    }

    public function resetPassword(User $admin)
    {
        $this->ensureAdmin($admin);

        $new = Str::password(10, symbols: false);
        $admin->password = $new;
        $admin->save();

        return back()->with('success', "Password baru untuk {$admin->email}: {$new} (catat sekarang, tidak ditampilkan lagi).");
    }

    public function destroy(User $admin)
    {
        $this->ensureAdmin($admin);
        $admin->delete();

        return back()->with('success', 'Akun admin dihapus.');
    }

    // Superadmin hanya boleh mengelola akun admin, bukan akun lain
    private function ensureAdmin(User $user): void
    {
        abort_unless(in_array($user->role, $this->adminRoles(), true), 404);
    }
}