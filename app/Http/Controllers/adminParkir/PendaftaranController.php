<?php

namespace App\Http\Controllers\adminParkir;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberRequest;
use App\Models\FaceProfile;
use App\Models\Member;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    public function index(): View
    {
        return view('admin_parkir.pendaftaran');
    }

    public function store(StoreMemberRequest $request)
    {
        $data = $request->validated();
        $photo = $request->file('photo');

        // 1. Ekstrak embedding dulu, sebelum menyentuh database
        $response = Http::timeout(30)
            ->attach('photo', file_get_contents($photo->getRealPath()), 'photo.jpg')
            ->post('http://127.0.0.1:5000/extract-embedding');

        if ($response->failed() || !$response->json('embedding')) {
            return back()
                ->withInput()
                ->withErrors(['photo' => $response->json('error') ?? 'Layanan AI tidak merespons.']);
        }

        $embedding = $response->json('embedding');

        // 2. Baru simpan semuanya
        DB::transaction(function () use ($data, $photo, $embedding) {
            $member = Member::create([
                'name'      => $data['name'],
                'phone'     => $data['phone'] ?? null,
                'email'     => $data['email'] ?? null,
                'is_active' => true,
            ]);

            foreach ($data['plates'] as $i => $plate) {
                Vehicle::create([
                    'member_id'    => $member->id,
                    'plate_number' => strtoupper($plate),
                    'vehicle_type' => $data['vehicle_types'][$i] ?? 'mobil',
                ]);
            }

            FaceProfile::create([
                'member_id'  => $member->id,
                'photo_path' => $photo->store('faces', 'public'),
                'embedding'  => $embedding, // nama kolom sesuaikan dengan migrasimu
            ]);
        });

        return redirect()->route('parkir.dashboard')
            ->with('success', 'Member berhasil didaftarkan.');
    }
}