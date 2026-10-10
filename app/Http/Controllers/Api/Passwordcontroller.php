<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'confirmed', 'different:current_password', Password::min(8)],
        ], [
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.different' => 'Kata sandi baru tidak boleh sama dengan yang lama.',
            'password.min'       => 'Kata sandi baru minimal 8 karakter.',
        ]);

        $member = $request->user();

        if (! Hash::check($data['current_password'], $member->password)) {
            return response()->json([
                'message' => 'Kata sandi saat ini salah.',
                'errors'  => ['current_password' => ['Kata sandi saat ini salah.']],
            ], 422);
        }

        // Cast 'hashed' di model Member otomatis meng-hash nilai ini.
        $member->password = $data['password'];
        $member->must_change_password = false;
        $member->save();

        // Cabut token di perangkat lain, token perangkat ini tetap hidup.
        $member->tokens()
            ->where('id', '!=', $request->user()->currentAccessToken()->id)
            ->delete();

        return response()->json([
            'message' => 'Kata sandi berhasil diganti.',
            'user'    => [
                'id'                   => $member->id,
                'name'                 => $member->name,
                'phone'                => $member->phone,
                'must_change_password' => false,
            ],
        ]);
    }
}