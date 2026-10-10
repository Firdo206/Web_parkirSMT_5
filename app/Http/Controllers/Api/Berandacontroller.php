<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BerandaController extends Controller
{
    public function index(Request $request)
    {
        $member = $request->user();

        // Langganan dengan masa berlaku paling akhir. Kalau sudah lewat, tetap
        // dikirim supaya aplikasi bisa menampilkan "Kedaluwarsa".
        $sub = $member->subscriptions()
            ->with('plan')
            ->orderByDesc('end_date')
            ->first();

        // Satu kendaraan = satu baris. Kalau punya sesi parkir aktif berarti "di dalam".
        $kendaraan = $member->vehicles()
            ->with(['parkingSessions' => fn ($q) => $q->active()->latest('entry_time')])
            ->orderBy('id')
            ->get()
            ->map(fn ($v) => [
                'id'          => $v->id,
                'plat'        => $v->plate_number,
                'jenis'       => $v->vehicle_type,
                'masuk_sejak' => $v->parkingSessions->first()?->entry_time?->toIso8601String(),
            ]);

        return response()->json([
            'paket' => $sub ? [
                'nama'     => $sub->plan?->name ?? '-',
                'mulai'    => $sub->start_date?->toDateString(),
                'berakhir' => $sub->end_date?->toDateString(),
                'status'   => $sub->isActive() ? 'aktif' : 'tidak_aktif',
            ] : null,
            'kendaraan' => $kendaraan,
        ]);
    }
}