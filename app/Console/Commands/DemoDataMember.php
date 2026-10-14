<?php

namespace App\Console\Commands;

use App\Models\AccessRequest;
use App\Models\Member;
use App\Models\ParkingSession;
use App\Models\SecurityAlert;
use App\Models\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Mengisi data palsu untuk satu member supaya layar Riwayat dan Notifikasi
 * di aplikasi mobile bisa dites tanpa perangkat IoT (ESP8266 + kamera).
 *
 *   php artisan demo:member 0812xxxxxxx             isi riwayat lengkap
 *   php artisan demo:member 0812xxxxxxx --pending   tambah 1 permintaan "menunggu"
 *   php artisan demo:member 0812xxxxxxx --hapus     hapus riwayat member ini
 *
 * Hanya jalan di APP_ENV=local.
 */
class DemoDataMember extends Command
{
    protected $signature = 'demo:member
                            {phone : No. HP member, format bebas (0812..., +62812...)}
                            {--pending : Hanya buat 1 permintaan akses berstatus "menunggu"}
                            {--hapus : Hapus semua kunjungan dan percobaan akses member ini}';

    protected $description = 'Isi data demo (kunjungan, percobaan akses) untuk satu member, khusus lokal';

    public function handle(): int
    {
        if (! app()->environment('local')) {
            $this->error('Perintah ini hanya boleh dijalankan di APP_ENV=local.');

            return self::FAILURE;
        }

        $member = Member::where('phone', $this->normalizePhone((string) $this->argument('phone')))->first();

        if (! $member) {
            $this->error('Member dengan No. HP itu tidak ditemukan. Daftarkan dulu lewat halaman Pendaftaran.');

            return self::FAILURE;
        }

        $kendaraan = $member->vehicles()->orderBy('id')->get();

        if ($kendaraan->isEmpty()) {
            $this->error('Member ini belum punya kendaraan. Tambahkan kendaraan dulu.');

            return self::FAILURE;
        }

        if ($this->option('hapus')) {
            return $this->hapus($member);
        }

        if ($this->option('pending')) {
            $this->buatPending($member, $kendaraan->first());
            $this->info("1 permintaan 'menunggu' dibuat untuk {$member->name}. Cek tab Riwayat > Perlu perhatian.");

            return self::SUCCESS;
        }

        DB::transaction(fn () => $this->isiLengkap($member, $kendaraan));

        $this->info("Data demo untuk {$member->name} selesai dibuat:");
        $this->line('  - 7 kunjungan selesai (hari-hari sebelumnya)');
        $this->line('  - 1 kunjungan yang masih di dalam (kalau ada kendaraan yang bebas)');
        $this->line("  - 4 percobaan akses: ditolak pemilik, tidak dijawab, ditolak sistem, menunggu");

        return self::SUCCESS;
    }

    private function isiLengkap(Member $member, $kendaraan): void
    {
        // Pakai status "selesai" yang sudah dipakai data asli kalau ada.
        $statusSelesai = ParkingSession::where('status', '!=', 'active')->value('status') ?? 'completed';

        // [berapa hari lalu, jam masuk, lama (jam), indeks kendaraan]
        $kunjungan = [
            [1, 8, 9, 0], [2, 9, 4, 0], [2, 13, 2, 1], [3, 7, 10, 0],
            [5, 8, 8, 0], [6, 10, 3, 1], [8, 8, 9, 0],
        ];

        foreach ($kunjungan as [$hari, $jam, $lama, $idx]) {
            $v = $kendaraan[$idx % $kendaraan->count()];
            $masuk = now()->subDays($hari)->setTime($jam, rand(0, 50));
            $keluar = $masuk->copy()->addMinutes((int) ($lama * 60) + rand(0, 25));

            $in = $this->permintaan($member, [
                'plate_detected' => $v->plate_number,
                'requested_at' => $masuk,
                'responded_at' => $masuk->copy()->addSeconds(2),
            ]);
            $out = $this->permintaan($member, [
                'plate_detected' => $v->plate_number,
                'requested_at' => $keluar,
                'responded_at' => $keluar->copy()->addSeconds(2),
            ]);

            ParkingSession::create([
                'member_id' => $member->id,
                'vehicle_id' => $v->id,
                'entry_access_request_id' => $in->id,
                'exit_access_request_id' => $out->id,
                'entry_time' => $masuk,
                'exit_time' => $keluar,
                'status' => $statusSelesai,
            ]);
        }

        // Satu kendaraan yang sedang di dalam (kalau ada yang belum punya sesi aktif)
        $bebas = $kendaraan->first(
            fn (Vehicle $v) => ! ParkingSession::where('vehicle_id', $v->id)->where('status', 'active')->exists()
        );

        if ($bebas) {
            $masuk = now()->subMinutes(130);
            $in = $this->permintaan($member, [
                'plate_detected' => $bebas->plate_number,
                'requested_at' => $masuk,
                'responded_at' => $masuk->copy()->addSeconds(2),
            ]);

            ParkingSession::create([
                'member_id' => $member->id,
                'vehicle_id' => $bebas->id,
                'entry_access_request_id' => $in->id,
                'exit_access_request_id' => null,
                'entry_time' => $masuk,
                'exit_time' => null,
                'status' => 'active',
            ]);
        }

        // Percobaan akses yang tidak normal
        $plat = $kendaraan->first()->plate_number;

        $ditolak = $this->permintaan($member, [
            'plate_detected' => $plat,
            'face_match' => false,
            'status' => 'rejected_by_member',
            'requested_at' => now()->subDay()->setTime(19, 12),
            'responded_at' => now()->subDay()->setTime(19, 12, 40),
        ]);
        $this->alert($ditolak, 'face_mismatch_rejected');

        $timeout = $this->permintaan($member, [
            'plate_detected' => $plat,
            'face_match' => false,
            'status' => 'timeout',
            'requested_at' => now()->subDays(3)->setTime(21, 5),
            'responded_at' => null,
        ]);
        $this->alert($timeout, 'timeout_no_response');

        $this->permintaan($member, [
            'plate_detected' => 'B9999XYZ',
            'plate_match' => false,
            'face_match' => false,
            'status' => 'denied',
            'requested_at' => now()->subDays(4)->setTime(14, 30),
            'responded_at' => now()->subDays(4)->setTime(14, 30, 3),
        ]);

        $this->buatPending($member, $kendaraan->first());
    }

    /** Permintaan yang menunggu jawaban pemilik, waktunya "baru saja". */
    private function buatPending(Member $member, Vehicle $v): AccessRequest
    {
        return $this->permintaan($member, [
            'plate_detected' => $v->plate_number,
            'face_match' => false,
            'status' => 'pending',
            'requested_at' => now(),
            'responded_at' => null,
        ]);
    }

    private function hapus(Member $member): int
    {
        if (! $this->confirm("Hapus SEMUA kunjungan dan percobaan akses milik {$member->name}?")) {
            return self::SUCCESS;
        }

        DB::transaction(function () use ($member) {
            $idPermintaan = AccessRequest::where('member_id', $member->id)->pluck('id');

            // Urutan penting: sesi dulu (merujuk ke permintaan), lalu alert, lalu permintaan.
            ParkingSession::where('member_id', $member->id)->delete();
            SecurityAlert::whereIn('access_request_id', $idPermintaan)->delete();
            AccessRequest::where('member_id', $member->id)->delete();
        });

        $this->info('Riwayat member ini sudah dihapus.');

        return self::SUCCESS;
    }

    private function permintaan(Member $member, array $atribut): AccessRequest
    {
        return AccessRequest::create(array_merge([
            'member_id' => $member->id,
            'plate_match' => true,
            'face_match' => true,
            'status' => 'auto_approved',
        ], $atribut));
    }

    /** Bukan "alert": nama itu sudah dipakai Command::alert() bawaan Laravel. */
    private function buatAlert(AccessRequest $permintaan, string $tipe): void
    {
        // forceCreate: isi $fillable SecurityAlert belum saya lihat.
        SecurityAlert::forceCreate([
            'access_request_id' => $permintaan->id,
            'type' => $tipe,
            'status' => 'open',
        ]);
    }

    /** Sama dengan AuthController: "+62 812-..." -> "0812..." */
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '62')) {
            return '0'.substr($digits, 2);
        }
        if (str_starts_with($digits, '8')) {
            return '0'.$digits;
        }

        return $digits;
    }
}