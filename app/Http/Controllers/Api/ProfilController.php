<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfilController extends Controller
{
    /** GET /api/profil : data diri member yang sedang login (hanya baca). */
    public function show(Request $request): JsonResponse
    {
        $member = $this->member($request);
        $wajah = $member->faceProfile;

        return response()->json([
            'data' => [
                'nama' => $member->name,
                'phone' => $member->phone,
                'email' => $member->email,
                'aktif' => (bool) $member->is_active,
                'terdaftar_sejak' => $member->created_at?->toIso8601String(),

                'wajah' => [
                    'terdaftar' => $wajah !== null,
                    // dipakai app sebagai penanda cache: berubah kalau foto diganti petugas
                    'versi' => $wajah?->updated_at?->timestamp,
                ],

                'kendaraan' => $member->vehicles()->orderBy('id')->get()->map(fn ($v) => [
                    'id' => $v->id,
                    'plat' => $this->formatPlate($v->plate_number),
                    'jenis' => $v->vehicle_type,
                ])->values(),

                'kontak_petugas' => [
                    'whatsapp' => $this->nomorWhatsapp(config('parkvisi.kontak_petugas.whatsapp')),
                    'telepon' => config('parkvisi.kontak_petugas.telepon') ?: null,
                ],
            ],
        ]);
    }

    /**
     * GET /api/profil/foto : foto wajah milik member yang sedang login.
     * Tidak ada parameter id, jadi member tidak mungkin meminta foto orang lain.
     */
    public function foto(Request $request): StreamedResponse|JsonResponse
    {
        $profil = $this->member($request)->faceProfile;

        if (! $profil || ! $profil->photo_path) {
            return response()->json(['message' => 'Foto wajah belum terdaftar.'], 404);
        }

        // 'public' hanya untuk member yang didaftarkan sebelum foto dipindah ke disk privat
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($profil->photo_path)) {
                return Storage::disk($disk)->response($profil->photo_path, null, [
                    'Cache-Control' => 'private, max-age=86400',
                ]);
            }
        }

        return response()->json(['message' => 'File foto tidak ditemukan.'], 404);
    }

    private function member(Request $request): Member
    {
        $member = $request->user();
        abort_unless($member instanceof Member, 403);

        return $member;
    }

    /** "B1234ABC" -> "B 1234 ABC" */
    private function formatPlate(string $plate): string
    {
        $plate = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $plate));

        if (preg_match('/^([A-Z]{1,2})(\d{1,4})([A-Z]{0,3})$/', $plate, $m)) {
            return trim("{$m[1]} {$m[2]} {$m[3]}");
        }

        return $plate;
    }

    /** "0812-3456-7890" / "+62 812..." -> "628123456789" (format wa.me), atau null bila kosong. */
    private function nomorWhatsapp(?string $nomor): ?string
    {
        $digit = preg_replace('/\D+/', '', (string) $nomor);

        if ($digit === '') {
            return null;
        }

        if (str_starts_with($digit, '0')) {
            return '62'.substr($digit, 1);
        }

        if (str_starts_with($digit, '8')) {
            return '62'.$digit;
        }

        return $digit;
    }
}