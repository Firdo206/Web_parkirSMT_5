<?php

namespace App\Services;

use App\Models\AccessRequest;
use App\Models\Member;
use App\Models\ParkingGateState;
use App\Models\ParkingSession;
use App\Models\Vehicle;

/**
 * Dipanggil saat plat cocok dengan kendaraan member, tetapi wajah pengemudi
 * tidak dikenali atau milik member lain. Palang tetap tertutup, dan pemilik
 * kendaraan diminta memutuskan lewat aplikasi.
 */
class DriverMismatchService
{
    public const TIMEOUT_SECONDS = 60;

    // Sengaja memuat teks "WAJAH TIDAK COCOK": plateResult() memakainya untuk tahu
    // bahwa wajah sudah dinilai.
    public const STATUS_MENUNGGU = 'WAJAH TIDAK COCOK - MENUNGGU KONFIRMASI PEMILIK';

    /**
     * @param  string  $reason  face_unknown | face_other_member
     */
    public function raise(ParkingGateState $state, Member $member, Vehicle $vehicle, string $reason): AccessRequest
    {
        // Gerbang selalu ditegaskan ulang menunggu, walau hasil kamera dikirim dua kali.
        $state->update([
            'trigger' => false,
            'izin' => 'MENUNGGU',
            'status' => self::STATUS_MENUNGGU,
        ]);

        // Hindari notifikasi ganda untuk kejadian yang sama.
        $ada = AccessRequest::where('gate', $state->gate)
            ->where('vehicle_id', $vehicle->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();

        if ($ada) {
            return $ada;
        }

        $arah = ParkingSession::where('vehicle_id', $vehicle->id)->active()->exists() ? 'exit' : 'entry';

        return AccessRequest::create([
            'member_id' => $member->id,
            'gate' => $state->gate,
            'vehicle_id' => $vehicle->id,
            'direction' => $arah,
            'reason' => $reason,
            'plate_detected' => $state->plate_detected ?? $vehicle->plate_number,
            'plate_match' => true,
            'face_match' => false, // wajah tidak cocok dengan pemilik plat
            'status' => 'pending',
            'requested_at' => now(),
            'expires_at' => now()->addSeconds(self::TIMEOUT_SECONDS),
        ]);
    }
}