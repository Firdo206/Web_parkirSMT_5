<?php

namespace App\Http\Middleware;

use App\Models\Member;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dipasang di grup auth:sanctum untuk API mobile.
 *
 * 1. Member yang dinonaktifkan admin langsung ditolak, token lamanya dicabut.
 * 2. Selama must_change_password aktif, hanya /me, /logout, dan
 *    /password/change yang boleh diakses. Sisanya 403 dengan
 *    code "must_change_password" supaya aplikasi bisa mengarahkan ke layar ganti sandi.
 */
class EnsureMemberCanAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $member = $request->user();

        if (! $member instanceof Member) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        if (! $member->is_active) {
            $member->tokens()->delete();

            return response()->json([
                'message' => 'Akun dinonaktifkan. Hubungi admin parkir.',
                'code'    => 'account_inactive',
            ], 403);
        }

        if ($member->must_change_password
            && ! $request->is('api/me', 'api/logout', 'api/password/change')) {
            return response()->json([
                'message' => 'Anda harus mengganti kata sandi terlebih dahulu.',
                'code'    => 'must_change_password',
            ], 403);
        }

        return $next($request);
    }
}