<?php

namespace App\Http\Controllers\adminParkir;

use App\Exceptions\FaceEnrollmentException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberRequest;
use App\Models\FaceProfile;
use App\Models\Member;
use App\Models\SubscriptionPlan;
use App\Models\Vehicle;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class PendaftaranController extends Controller
{
    /** Jumlah minimal frame yang dikirim browser (Python tetap menyaring yang wajahnya terdeteksi). */
    private const MIN_FRAMES = 15;

    public function index(): View
    {
        return view('admin_parkir.pendaftaran', [
            'plans' => SubscriptionPlan::where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Banyak frame wajah dari kamera (name="photos[]"), seperti 30 foto di registrasi.py
        /** @var UploadedFile[] $photos */
        $photos = array_values((array) $request->file('photos'));

        if (count($photos) < self::MIN_FRAMES) {
            return back()->withInput()->withErrors([
                'photos' => 'Foto wajah kurang. Ambil ulang dari kamera sampai semua foto terkumpul.',
            ]);
        }

        // 1. Samakan format No. HP & plat nomor, lalu cek duplikat sebelum menyentuh Python/DB
        $phone = $this->normalizePhone((string) ($data['phone'] ?? ''));

        if (! preg_match('/^0\d{8,14}$/', $phone)) {
            return back()->withInput()->withErrors([
                'phone' => 'No. HP wajib diisi dengan benar (9–15 digit) karena dipakai untuk login aplikasi.',
            ]);
        }

        if (Member::where('phone', $phone)->exists()) {
            return back()->withInput()->withErrors(['phone' => 'No. HP ini sudah terdaftar sebagai member.']);
        }

        $plates = [];
        foreach ((array) ($data['plates'] ?? []) as $i => $plate) {
            $number = $this->normalizePlate((string) $plate);

            if ($number !== '') {
                $plates[] = [
                    'number' => $number,
                    'type' => ($data['vehicle_types'][$i] ?? 'mobil') === 'motor' ? 'motor' : 'mobil',
                ];
            }
        }

        $numbers = array_column($plates, 'number');

        if ($numbers === []) {
            return back()->withInput()->withErrors(['plates' => 'Isi minimal satu plat nomor.']);
        }

        if (count($numbers) !== count(array_unique($numbers))) {
            return back()->withInput()->withErrors(['plates' => 'Ada plat nomor yang diisi dua kali.']);
        }

        $taken = Vehicle::whereIn('plate_number', $numbers)->pluck('plate_number');

        if ($taken->isNotEmpty()) {
            return back()->withInput()->withErrors([
                'plates' => 'Plat nomor sudah terdaftar: '.$taken->map(fn ($p) => $this->formatPlate($p))->implode(', '),
            ]);
        }

        // 2. Paket & tagihan dihitung di server (angka dari browser tidak dipercaya)
        $plan = SubscriptionPlan::where('is_active', true)->find($data['plan_id']);

        if (! $plan) {
            return back()->withInput()->withErrors(['plan_id' => 'Paket tidak ditemukan atau sudah tidak aktif.']);
        }

        $total = 0;
        foreach ($plates as $plate) {
            $total += (float) ($plate['type'] === 'motor' ? $plan->price_motor : $plan->price_mobil);
        }

        // Hari terakhir ikut dihitung: mulai 10 Okt + 30 hari berakhir 8 Nov.
        $start = Carbon::parse($data['start_date'])->startOfDay();
        $end = $start->copy()->addDays($plan->duration_days - 1);

        // 3. Simpan SATU foto representatif (frame tengah) di disk privat sebagai profil wajah.
        //    Seluruh frame hanya dikirim ke Python untuk training, tidak disimpan di Laravel.
        $path = $photos[intdiv(count($photos), 2)]->store('faces', 'local');
        $password = $this->generatePassword();
        $adminId = $request->user()?->id;
        $enrolledId = null; // member yang wajahnya sudah masuk ke Python

        // 4. Semua data dalam satu transaksi. Enroll wajah ke Python dilakukan paling akhir:
        //    kalau gagal, exception membatalkan seluruh transaksi database.
        try {
            $member = DB::transaction(function () use (
                $data, $phone, $plates, $photos, $path, $password, $plan, $start, $end, $total, $adminId, &$enrolledId
            ) {
                $member = Member::create([
                    'name' => $data['name'],
                    'phone' => $phone,
                    'email' => $data['email'] ?? null,
                    'is_active' => true,
                ]);

                // Akun aplikasi diisi langsung (bukan lewat create) supaya tidak bergantung pada $fillable.
                // Hash::make aman walau model memakai cast 'hashed' (tidak di-hash dua kali).
                $member->password = Hash::make($password);
                $member->must_change_password = true;
                $member->save();

                foreach ($plates as $plate) {
                    Vehicle::create([
                        'member_id' => $member->id,
                        'plate_number' => $plate['number'],
                        'vehicle_type' => $plate['type'],
                    ]);
                }

                FaceProfile::create([
                    'member_id' => $member->id,
                    'photo_path' => $path,
                ]);

                // Langganan pertama langsung aktif
                $subscription = $member->subscriptions()->create([
                    'subscription_plan_id' => $plan->id,
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                    'status' => 'active',
                ]);

                $subscription->payments()->create([
                    'amount_due' => $total,
                    'amount_paid' => (int) $data['amount_paid'],
                    'method' => $data['payment_method'],
                    'paid_at' => now(),
                    'created_by' => $adminId,
                ]);

                // Terakhir: kirim semua frame ke Python
                // (deteksi Haar cascade tiap frame -> preprocessing -> training LBPH)
                $this->enrollFace($member->id, $photos);
                $enrolledId = $member->id;

                return $member;
            });
        } catch (FaceEnrollmentException $e) {
            Storage::disk('local')->delete($path);

            return back()->withInput()->withErrors(['photos' => $e->getMessage()]);
        } catch (UniqueConstraintViolationException) {
            Storage::disk('local')->delete($path);
            $this->removeFace($enrolledId);

            return back()->withInput()->withErrors(['name' => 'No. HP atau plat nomor sudah terdaftar.']);
        } catch (Throwable $e) {
            Storage::disk('local')->delete($path);
            $this->removeFace($enrolledId);
            report($e);

            return back()->withInput()->withErrors(['name' => 'Gagal menyimpan data member. Silakan coba lagi.']);
        }

        // 5. Kembali ke form; kata sandi sementara hanya tampil sekali lewat flash session
        return back()->with([
            'success' => 'Member berhasil didaftarkan.',
            'created' => [
                'name' => $member->name,
                'phone' => $phone,
                'password' => $password,
                'vehicles' => array_map(fn ($p) => $this->formatPlate($p['number']), $plates),
                'plan' => $plan->name,
                'start' => $start->copy()->locale('id')->translatedFormat('j M Y'),
                'end' => $end->copy()->locale('id')->translatedFormat('j M Y'),
            ],
        ]);
    }

    /**
     * Kirim semua frame ke Python: tiap frame dideteksi (Haar cascade) -> preprocessing -> training LBPH.
     * Field multipart bernama "photos" diulang per frame; Flask membacanya lewat request.files.getlist('photos').
     *
     * @param  UploadedFile[]  $photos
     */
    private function enrollFace(int $memberId, array $photos): void
    {
        try {
            $request = Http::timeout(120); // deteksi 30 frame + training ulang bisa memakan waktu

            foreach ($photos as $i => $photo) {
                $request = $request->attach(
                    'photos',
                    file_get_contents($photo->getRealPath()),
                    sprintf('frame_%02d.jpg', $i + 1)
                );
            }

            $response = $request->post(
                config('services.cv.url', 'http://127.0.0.1:5000').'/enroll',
                ['member_id' => $memberId]
            );
        } catch (ConnectionException) {
            throw new FaceEnrollmentException(
                'Layanan pengenalan wajah tidak bisa dihubungi. Pastikan Python service sedang berjalan.'
            );
        }

        if ($response->failed()) {
            throw new FaceEnrollmentException(
                $response->json('error') ?? 'Layanan pengenalan wajah tidak merespons.'
            );
        }
    }

    /** Bersihkan sampel wajah di Python bila transaksi gagal setelah enroll berhasil. */
    private function removeFace(?int $memberId): void
    {
        if ($memberId === null) {
            return;
        }

        try {
            Http::timeout(30)->delete(config('services.cv.url', 'http://127.0.0.1:5000')."/members/{$memberId}");
        } catch (Throwable $e) {
            report($e);
        }
    }

    /** "+62 812-3456-7890" / "6281234567890" / "81234567890" -> "081234567890" */
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

    /** "b 1234 abc" / "B-1234-ABC" -> "B1234ABC" (format simpan, cocok dengan hasil OCR) */
    private function normalizePlate(string $plate): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $plate));
    }

    /** "B1234ABC" -> "B 1234 ABC" (format tampil) */
    private function formatPlate(string $plate): string
    {
        if (preg_match('/^([A-Z]{1,2})(\d{1,4})([A-Z]{0,3})$/', $plate, $m)) {
            return trim("{$m[1]} {$m[2]} {$m[3]}");
        }

        return $plate;
    }

    /** Kata sandi acak 8 karakter tanpa huruf/angka yang mirip (0/O, 1/l/I) */
    private function generatePassword(int $length = 8): string
    {
        $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $out = '';

        for ($i = 0; $i < $length; $i++) {
            $out .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $out;
    }
}
