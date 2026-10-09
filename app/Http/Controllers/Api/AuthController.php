<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'phone'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $member = Member::where('phone', $this->normalizePhone($data['phone']))->first();

        if (! $member || ! $member->password || ! Hash::check($data['password'], $member->password)) {
            return response()->json(['message' => 'No. HP atau password salah.'], 422);
        }

        if (! $member->is_active) {
            return response()->json(['message' => 'Akun dinonaktifkan. Hubungi admin parkir.'], 403);
        }

        return response()->json([
            'token' => $member->createToken('mobile')->plainTextToken,
            'user'  => $this->payload($member),
        ]);
    }

    public function me(Request $request)
    {
        return response()->json(['user' => $this->payload($request->user())]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil keluar.']);
    }

    private function payload(Member $m): array
    {
        return [
            'id' => $m->id,
            'name' => $m->name,
            'phone' => $m->phone,
            'must_change_password' => (bool) $m->must_change_password,
        ];
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '62')) return '0'.substr($digits, 2);
        if (str_starts_with($digits, '8'))  return '0'.$digits;

        return $digits;
    }
}