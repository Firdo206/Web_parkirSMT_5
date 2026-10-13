<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccessRequest;
use App\Models\SecurityAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotifikasiController extends Controller
{
    // Batas waktu member merespons (detik). Samakan dengan timeout di gerbang.
    private const RESPONSE_WINDOW = 60;

    public function index(Request $request)
    {
        $memberId = $request->user()->id;

        $this->expireStale($memberId);

        $items = AccessRequest::where('member_id', $memberId)
            ->where('status', '!=', 'auto_approved')
            ->orderByDesc('requested_at')
            ->limit(50)
            ->get()
            ->map(fn (AccessRequest $r) => $this->payload($r))
            ->values();

        return response()->json([
            'pending_count' => $items->where('needs_action', true)->count(),
            'data'          => $items,
        ]);
    }

    public function respond(Request $request, AccessRequest $accessRequest)
    {
        $data = $request->validate([
            'decision' => ['required', 'in:approve,reject'],
        ]);

        // Member hanya boleh merespons permintaan miliknya
        abort_unless($accessRequest->member_id === $request->user()->id, 404);

        return DB::transaction(function () use ($accessRequest, $data) {
            // lock supaya tidak bentrok dengan proses timeout / gerbang
            $row = AccessRequest::lockForUpdate()->findOrFail($accessRequest->id);

            if ($row->status !== 'pending') {
                return response()->json(['message' => 'Permintaan ini sudah diproses.'], 409);
            }

            $expired = now()->timestamp - $row->requested_at->timestamp > self::RESPONSE_WINDOW;

            if ($expired) {
                $row->update(['status' => 'timeout']);
                SecurityAlert::firstOrCreate([
                    'access_request_id' => $row->id,
                    'type'              => 'timeout_no_response',
                ]);

                return response()->json(['message' => 'Waktu merespons sudah habis.'], 409);
            }

            if ($data['decision'] === 'approve') {
                $row->update(['status' => 'approved_by_member', 'responded_at' => now()]);
            } else {
                $row->update(['status' => 'rejected_by_member', 'responded_at' => now()]);
                SecurityAlert::firstOrCreate([
                    'access_request_id' => $row->id,
                    'type'              => 'face_mismatch_rejected',
                ]);
            }

            return response()->json([
                'message' => $data['decision'] === 'approve' ? 'Akses diizinkan.' : 'Akses ditolak.',
                'data'    => $this->payload($row->fresh()),
            ]);
        });
    }

    /** Pending yang lewat batas waktu otomatis jadi timeout + alert. */
    private function expireStale(int $memberId): void
    {
        $stale = AccessRequest::where('member_id', $memberId)
            ->where('status', 'pending')
            ->where('requested_at', '<', now()->subSeconds(self::RESPONSE_WINDOW))
            ->pluck('id');

        foreach ($stale as $id) {
            DB::transaction(function () use ($id) {
                $row = AccessRequest::lockForUpdate()->find($id);

                if ($row && $row->status === 'pending') {
                    $row->update(['status' => 'timeout']);
                    SecurityAlert::firstOrCreate([
                        'access_request_id' => $row->id,
                        'type'              => 'timeout_no_response',
                    ]);
                }
            });
        }
    }

    private function payload(AccessRequest $r): array
    {
        $pending = $r->status === 'pending';
        $elapsed = now()->timestamp - $r->requested_at->timestamp;

        [$title, $body] = match ($r->status) {
            'pending' => [
                'Konfirmasi akses diperlukan',
                "Kendaraan {$r->plate_detected} mencoba masuk, tetapi wajah pengemudi tidak cocok. Apakah ini Anda atau orang yang Anda izinkan?",
            ],
            'approved_by_member' => [
                'Akses diizinkan',
                "Anda mengizinkan kendaraan {$r->plate_detected} masuk.",
            ],
            'rejected_by_member' => [
                'Akses ditolak',
                "Anda menolak kendaraan {$r->plate_detected}. Laporan keamanan telah dibuat.",
            ],
            'timeout' => [
                'Tidak ada respons',
                "Permintaan akses untuk {$r->plate_detected} tidak dijawab sehingga akses ditolak.",
            ],
            default => [
                'Akses ditolak',
                "Kendaraan {$r->plate_detected} ditolak oleh sistem.",
            ],
        };

        return [
            'id'           => $r->id,
            'title'        => $title,
            'body'         => $body,
            'status'       => $r->status,
            'plate'        => $r->plate_detected,
            'needs_action' => $pending,
            'seconds_left' => $pending ? max(0, self::RESPONSE_WINDOW - $elapsed) : 0,
            'requested_at' => $r->requested_at->toIso8601String(),
        ];
    }
}