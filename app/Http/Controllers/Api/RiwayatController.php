<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccessRequest;
use App\Models\Member;
use App\Models\ParkingSession;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RiwayatController extends Controller
{
    private const PER_PAGE = 20;

    /**
     * Riwayat untuk member yang sedang login.
     *
     * GET /api/riwayat?filter=kunjungan|perhatian &vehicle_id= &page=
     *
     * - kunjungan (default): satu baris per kunjungan parkir (masuk, keluar, durasi).
     *   Kunjungan yang belum keluar ditandai di_dalam = true.
     * - perhatian: percobaan akses yang TIDAK normal (ditolak, belum dijawab, dsb).
     *   Catatan: tabel access_requests tidak menyimpan kendaraan, jadi vehicle_id diabaikan di sini.
     */
    public function index(Request $request): JsonResponse
    {
        $member = $request->user();
        abort_unless($member instanceof Member, 403);

        $filter = $request->query('filter') === 'perhatian' ? 'perhatian' : 'kunjungan';

        $kendaraan = Vehicle::where('member_id', $member->id)->orderBy('id')->get();
        $kendaraanById = $kendaraan->keyBy('id');

        if ($filter === 'perhatian') {
            $page = AccessRequest::query()
                ->where('member_id', $member->id)
                ->whereNotIn('status', ['auto_approved', 'approved_by_member'])
                ->orderByDesc('requested_at')
                ->orderByDesc('id')
                ->paginate(self::PER_PAGE);

            $items = $page->getCollection()->map(fn (AccessRequest $r) => [
                'id' => $r->id,
                'tipe' => 'percobaan',
                'waktu' => $this->iso($r->requested_at),
                'plat' => $this->formatPlate((string) $r->plate_detected),
                'hasil' => $this->hasil((string) $r->status),
            ])->values();
        } else {
            $vehicleId = $request->integer('vehicle_id') ?: null;

            $page = ParkingSession::query()
                ->where('member_id', $member->id)
                ->when($vehicleId, fn ($q) => $q->where('vehicle_id', $vehicleId))
                ->orderByDesc('entry_time')
                ->orderByDesc('id')
                ->paginate(self::PER_PAGE);

            $items = $page->getCollection()->map(function (ParkingSession $s) use ($kendaraanById) {
                $v = $kendaraanById->get($s->vehicle_id);

                return [
                    'id' => $s->id,
                    'tipe' => 'kunjungan',
                    'kendaraan' => [
                        'id' => $s->vehicle_id,
                        'plat' => $v ? $this->formatPlate($v->plate_number) : '-',
                        'jenis' => $v?->vehicle_type,
                    ],
                    'masuk' => $this->iso($s->entry_time),
                    'keluar' => $s->exit_time ? $this->iso($s->exit_time) : null,
                    'durasi_menit' => $s->exit_time ? $this->durasiMenit($s->entry_time, $s->exit_time) : null,
                    'di_dalam' => $s->status === 'active' && ! $s->exit_time,
                ];
            })->values();
        }

        return response()->json([
            'data' => $items,
            'meta' => [
                'filter' => $filter,
                'page' => $page->currentPage(),
                'next_page' => $page->hasMorePages() ? $page->currentPage() + 1 : null,
                'total' => $page->total(),
                // untuk chip pilihan kendaraan di layar
                'kendaraan' => $kendaraan->map(fn ($v) => [
                    'id' => $v->id,
                    'plat' => $this->formatPlate($v->plate_number),
                    'jenis' => $v->vehicle_type,
                ])->values(),
                // untuk penanda di tab "Perlu perhatian": jumlah percobaan yang menunggu jawaban member
                'menunggu' => AccessRequest::where('member_id', $member->id)->where('status', 'pending')->count(),
            ],
        ]);
    }

    /** Status di database -> istilah yang dipakai aplikasi. */
    private function hasil(string $status): string
    {
        return match ($status) {
            'auto_approved' => 'diizinkan',
            'approved_by_member' => 'diizinkan_pemilik',
            'rejected_by_member' => 'ditolak_pemilik',   // member menjawab "Bukan saya"
            'timeout' => 'tidak_dijawab',
            'pending' => 'menunggu',
            default => 'ditolak',                         // 'denied'
        };
    }

    private function iso($value): string
    {
        return Carbon::parse($value)->toIso8601String();
    }

    /** Selisih menit (dibulatkan ke bawah), aman terhadap perbedaan versi Carbon. */
    private function durasiMenit($masuk, $keluar): int
    {
        return max(0, intdiv(Carbon::parse($keluar)->timestamp - Carbon::parse($masuk)->timestamp, 60));
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
}