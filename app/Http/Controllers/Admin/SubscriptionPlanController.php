<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::ordered()->get();

        return view('admin.subscription-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.subscription-plans.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);
        $validated['is_popular'] = $request->boolean('is_popular');
        $validated['is_active']  = $request->boolean('is_active');

        SubscriptionPlan::create($validated);

        return redirect()
            ->route('admin.paket-harga.index')
            ->with('success', 'Paket harga berhasil ditambahkan.');
    }

    public function edit(SubscriptionPlan $paketHarga)
    {
        return view('admin.subscription-plans.edit', ['plan' => $paketHarga]);
    }

    public function update(Request $request, SubscriptionPlan $paketHarga)
    {
        $validated = $this->validateData($request, $paketHarga->id);
        $validated['is_popular'] = $request->boolean('is_popular');
        $validated['is_active']  = $request->boolean('is_active');

        $paketHarga->update($validated);

        return redirect()
            ->route('admin.paket-harga.index')
            ->with('success', 'Paket harga berhasil diperbarui.');
    }

    public function destroy(SubscriptionPlan $paketHarga)
    {
        $paketHarga->delete();

        return redirect()
            ->route('admin.paket-harga.index')
            ->with('success', 'Paket harga berhasil dihapus.');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'          => 'required|string|max:100',
            'duration_days' => 'required|integer|min:1',
            'price_motor'   => 'required|numeric|min:0',
            'price_mobil'   => 'required|numeric|min:0',
            'description'   => 'nullable|string|max:500',
            'features'      => 'nullable|string|max:2000',
            'sort_order'    => 'nullable|integer|min:0',
        ]);
    }
}