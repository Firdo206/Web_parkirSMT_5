<?php

namespace App\Http\Controllers\adminParkir;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PelangganController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status', '');

        $members = Member::with(['vehicles', 'faceProfile', 'latestSubscription.plan'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereHas('vehicles', function ($v) use ($search) {
                          $v->where('plate_number', 'like', "%{$search}%");
                      });
                });
            })
            ->when(in_array($status, ['aktif', 'nonaktif'], true), function ($query) use ($status) {
                $query->where('is_active', $status === 'aktif');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total'    => Member::count(),
            'aktif'    => Member::where('is_active', true)->count(),
            'nonaktif' => Member::where('is_active', false)->count(),
        ];

           return view('admin_parkir.pelanggan', compact('members', 'stats', 'search', 'status'));
    }
}