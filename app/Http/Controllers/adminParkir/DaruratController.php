<?php

namespace App\Http\Controllers\adminParkir;

use App\Http\Controllers\Controller;
use App\Models\EmergencyGateLog;
use App\Models\ParkingGateState;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DaruratController extends Controller
{
    /** Daftar gerbang. Tambah di sini kalau nanti ada gerbang keluar. */
    private const GATES = [
        'gate_utama' => 'Gerbang utama',
        'gate_keluar' => 'Gerbang keluar',
    ];

    public function index(): View
    {
        return view('admin_parkir.darurat', [
            'gates' => self::GATES,
            'states' => ParkingGateState::whereIn('gate', array_keys(self::GATES))
                ->get()->keyBy('gate'),
            'logs' => EmergencyGateLog::with('admin')->latest()->limit(20)->get(),
        ]);
    }

    public function open(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'gate'   => ['required', 'in:'.implode(',', array_keys(self::GATES))],
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ], [
            'reason.required' => 'Alasan wajib diisi.',
            'reason.min' => 'Alasan minimal 5 karakter.',
        ]);

        DB::transaction(function () use ($data, $request) {
            EmergencyGateLog::create([
                'gate' => $data['gate'],
                'reason' => $data['reason'],
                'issued_by' => $request->user()->id,
            ]);

            ParkingGateState::updateOrCreate(
                ['gate' => $data['gate']],
                [
                    'trigger' => false,
                    'izin' => 'BUKA',
                    'status' => 'PALANG DIBUKA MANUAL - DARURAT',
                    'face_match' => false,
                    'face_member_id' => null,
                    'plate_match' => false,
                    'plate_member_id' => null,
                    'plate_vehicle_id' => null,
                    'plate_detected' => null,
                ]
            );
        });

        return back()->with('success', 'Perintah buka palang dikirim ke '.self::GATES[$data['gate']].'.');
    }
}