<?php

namespace App\Services;

use App\Exceptions\SlotPenuhException;
use App\Models\ParkingSession;
use App\Models\ParkingSlot;
use App\Models\Vehicle;

/**
 * Slot = jatah kendaraan member per jenis (mobil / motor).
 *
 * "Terisi" = kendaraan milik member aktif yang langganannya belum berakhir.
 * Langganan yang habis otomatis melepas slotnya, tanpa proses terpisah.
 */
class SlotService
{
    public const JENIS = ['mobil'];
    private const KAPASITAS_AWAL = 20;

    public function kapasitas(string $jenis): int
    {
        return (int) ParkingSlot::firstOrCreate(
            ['vehicle_type' => $jenis],
            ['capacity' => self::KAPASITAS_AWAL]
        )->capacity;
    }

    public function terisi(string $jenis): int
    {
        return Vehicle::where('vehicle_type', $jenis)
            ->whereHas('member', function ($member) {
                $member->where('is_active', true)
                    ->whereHas('subscriptions', function ($langganan) {
                        $langganan->where('status', 'active')
                            ->whereDate('end_date', '>=', now()->toDateString());
                    });
            })
            ->count();
    }

    /** Kendaraan yang sedang berada di dalam area (hanya informasi). */
    public function diDalam(string $jenis): int
    {
        return ParkingSession::active()
            ->whereHas('vehicle', fn ($v) => $v->where('vehicle_type', $jenis))
            ->count();
    }

    public function ringkasan(): array
    {
        $hasil = [];

        foreach (self::JENIS as $jenis) {
            $kapasitas = $this->kapasitas($jenis);
            $terisi = $this->terisi($jenis);

            $hasil[$jenis] = [
                'kapasitas' => $kapasitas,
                'terisi' => $terisi,
                'sisa' => max(0, $kapasitas - $terisi),
                'kelebihan' => max(0, $terisi - $kapasitas),
                'di_dalam' => $this->diDalam($jenis),
                'penuh' => $terisi >= $kapasitas,
            ];
        }

        return $hasil;
    }

    /**
     * Kunci baris kapasitas supaya dua pendaftaran bersamaan tidak sama-sama
     * mengambil slot terakhir. Panggil di dalam transaksi, sebelum menghitung.
     */
    public function kunci(): void
    {
        foreach (self::JENIS as $jenis) {
            $this->kapasitas($jenis); // pastikan barisnya ada
        }

        ParkingSlot::whereIn('vehicle_type', self::JENIS)->lockForUpdate()->get();
    }

    /**
     * @param  array<string,int>  $perJenis  contoh: ['mobil' => 2, 'motor' => 1]
     * @return string|null pesan galat, atau null kalau slotnya cukup
     */
    public function kekurangan(array $perJenis): ?string
    {
        $pesan = [];

        foreach (self::JENIS as $jenis) {
            $minta = (int) ($perJenis[$jenis] ?? 0);

            if ($minta === 0) {
                continue;
            }

            $kapasitas = $this->kapasitas($jenis);
            $terisi = $this->terisi($jenis);
            $sisa = max(0, $kapasitas - $terisi);

            if ($minta > $sisa) {
                $pesan[] = $sisa === 0
                    ? "Slot {$jenis} penuh ({$terisi} dari {$kapasitas} terisi)."
                    : "Slot {$jenis} tidak cukup: sisa {$sisa}, sedangkan yang didaftarkan {$minta}.";
            }
        }

        return $pesan === [] ? null : implode(' ', $pesan);
    }

    /** @throws SlotPenuhException */
    public function pastikanMuat(array $perJenis): void
    {
        if ($pesan = $this->kekurangan($perJenis)) {
            throw new SlotPenuhException($pesan);
        }
    }
}