<?php

namespace App\Http\Controllers\adminParkir;

use App\Http\Controllers\Controller;
use App\Models\ParkingSlot;
use App\Services\SlotService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SlotController extends Controller
{
    public function index(SlotService $slot): View
    {
        return view('admin_parkir.slot', ['slots' => $slot->ringkasan()]);
    }

    public function update(Request $request, SlotService $slot): RedirectResponse
    {
            $data = $request->validate([
            'capacity' => ['required', 'array'],
            'capacity.mobil' => ['required', 'integer', 'min:0', 'max:100000'],
        ], [
            'required' => ':attribute wajib diisi.',
            'integer' => ':attribute harus berupa angka bulat.',
            'min' => ':attribute tidak boleh kurang dari :min.',
            'max' => ':attribute maksimal :max.',
        ], [
            'capacity.mobil' => 'Kapasitas mobil',
        ]);;

        foreach (SlotService::JENIS as $jenis) {
            ParkingSlot::updateOrCreate(
                ['vehicle_type' => $jenis],
                ['capacity' => (int) $data['capacity'][$jenis]]
            );
        }

        // Kapasitas boleh diturunkan di bawah jumlah terisi; member yang ada tidak diusir,
        // tapi pendaftaran baru ditutup sampai ada langganan yang berakhir.
        $catatan = [];
        foreach ($slot->ringkasan() as $jenis => $s) {
            if ($s['kelebihan'] > 0) {
                $catatan[] = "slot {$jenis} terisi {$s['terisi']}, melebihi kapasitas {$s['kapasitas']}";
            }
        }

        $pesan = 'Kapasitas slot diperbarui.';
        if ($catatan !== []) {
            $pesan .= ' Catatan: '.implode('; ', $catatan).'. Pendaftaran baru ditutup sampai ada yang berakhir.';
        }

        return redirect()->route('parkir.slot')->with('success', $pesan);
    }
}