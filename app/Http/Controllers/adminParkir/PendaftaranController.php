<?php

namespace App\Http\Controllers\adminParkir;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberRequest;
use App\Models\FaceProfile;
use App\Models\Member;
use App\Models\Vehicle;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class PendaftaranController extends Controller
{
    public function index(): View
    {
        return view('admin_parkir.pendaftaran');
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $photo = $request->file('photo');

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
                $plates[] = ['number' => $number, 'type' => $data['vehicle_types'][$i] ?? 'mobil'];
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

        // 2. Ekstrak embedding dulu, sebelum menyentuh database
        try {
            $response = Http::timeout(30)
                ->attach('photo', file_get_contents($photo->getRealPath()), 'photo.jpg')
                ->post(config('services.cv.url', 'http://127.0.0.1:5000').'/extract-embedding');
        } catch (ConnectionException) {
            return back()->withInput()->withErrors([
                'photo' => 'Layanan pengenalan wajah tidak bisa dihubungi. Pastikan Python service sedang berjalan.',
            ]);
        }

        if ($response->failed() || ! $response->json('embedding')) {
            return back()->withInput()->withErrors([
                'photo' => $response->json('error') ?? 'Layanan pengenalan wajah tidak merespons.',
            ]);
        }

        $embedding = $response->json('embedding');

        // 3. Foto disimpan di disk privat (data biometrik), lalu semua data dalam satu transaksi
        $path = $photo->store('faces', 'local');
        $password = $this->generatePassword();

        try {
            $member = DB::transaction(function () use ($data, $phone, $plates, $embedding, $path, $password) {
                $member = Member::create([
                    'name' => $data['name'],
                    'phone' => $phone,
                    'email' => $data['email'] ?? null,
                    'is_active' => true,
                ]);

                // Akun aplikasi diisi langsung (bukan lewat create) supaya tidak bergantung pada $fillable.
                // Hash::make aman walau model nanti memakai cast 'hashed' (tidak di-hash dua kali).
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
                    'embedding' => $embedding,
                ]);

                return $member;
            });
        } catch (UniqueConstraintViolationException) {
            Storage::disk('local')->delete($path);

            return back()->withInput()->withErrors(['name' => 'No. HP atau plat nomor sudah terdaftar.']);
        } catch (Throwable $e) {
            Storage::disk('local')->delete($path);
            report($e);

            return back()->withInput()->withErrors(['name' => 'Gagal menyimpan data member. Silakan coba lagi.']);
        }

        // 4. Kembali ke form; kata sandi sementara hanya tampil sekali lewat flash session
        return back()->with([
            'success' => 'Member berhasil didaftarkan.',
            'created' => [
                'name' => $member->name,
                'phone' => $phone,
                'password' => $password,
                'vehicles' => array_map(fn ($p) => $this->formatPlate($p['number']), $plates),
            ],
        ]);
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