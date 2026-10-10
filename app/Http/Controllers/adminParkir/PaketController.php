<?php

namespace App\Http\Controllers\adminParkir;

use App\Http\Controllers\Controller;
use App\Models\MemberSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PaketController extends Controller
{
    public function index(): View
    {
        $plans = SubscriptionPlan::orderBy('sort_order')->orderBy('id')->get();

        // Berapa langganan yang pernah memakai tiap paket (menentukan boleh dihapus atau tidak)
        $dipakai = MemberSubscription::selectRaw('subscription_plan_id, COUNT(*) AS total')
            ->groupBy('subscription_plan_id')
            ->pluck('total', 'subscription_plan_id');

        return view('admin_parkir.paket', compact('plans', 'dipakai'));
    }

    public function create(): View
    {
        $plan = new SubscriptionPlan([
            'duration_days' => 30,
            'is_active' => true,
            'is_popular' => false,
            'sort_order' => (int) SubscriptionPlan::max('sort_order') + 1,
        ]);

        return view('admin_parkir.paket.form', ['plan' => $plan, 'dipakai' => 0]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        SubscriptionPlan::create($data + ['slug' => $this->uniqueSlug($data['name'])]);

        return redirect()->route('parkir.paket.index')
            ->with('success', 'Paket "'.$data['name'].'" berhasil ditambahkan.');
    }

    public function edit(SubscriptionPlan $plan): View
    {
        $dipakai = MemberSubscription::where('subscription_plan_id', $plan->id)->count();

        return view('admin_parkir.paket.form', compact('plan', 'dipakai'));
    }

    public function update(Request $request, SubscriptionPlan $plan): RedirectResponse
    {
        $data = $this->validated($request, $plan);

        // slug sengaja tidak diubah supaya referensi lama tidak putus
        $plan->update($data);

        return redirect()->route('parkir.paket.index')
            ->with('success', 'Paket "'.$plan->name.'" berhasil diperbarui.');
    }

    public function toggle(SubscriptionPlan $plan): RedirectResponse
    {
        $plan->update(['is_active' => ! $plan->is_active]);

        return back()->with('success', 'Paket "'.$plan->name.'" sekarang '.($plan->is_active ? 'aktif' : 'nonaktif').'.');
    }

    public function destroy(SubscriptionPlan $plan): RedirectResponse
    {
        $dipakai = MemberSubscription::where('subscription_plan_id', $plan->id)->count();

        if ($dipakai > 0) {
            return back()->with('error', 'Paket "'.$plan->name.'" sudah dipakai '.$dipakai.' langganan, jadi tidak bisa dihapus. Nonaktifkan saja.');
        }

        $nama = $plan->name;
        $plan->delete();

        return back()->with('success', 'Paket "'.$nama.'" dihapus.');
    }

    private function validated(Request $request, ?SubscriptionPlan $plan = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('subscription_plans', 'name')->ignore($plan?->id)],
            'duration_days' => ['required', 'integer', 'min:1', 'max:3650'],
            'price_motor' => ['required', 'integer', 'min:0', 'max:100000000'],
            'price_mobil' => ['required', 'integer', 'min:0', 'max:100000000'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], [
            'required' => ':attribute wajib diisi.',
            'integer' => ':attribute harus berupa angka bulat.',
            'min.numeric' => ':attribute minimal :min.',
            'max.numeric' => ':attribute maksimal :max.',
            'max.string' => ':attribute maksimal :max karakter.',
            'unique' => ':attribute sudah dipakai paket lain.',
        ], [
            'name' => 'Nama paket',
            'duration_days' => 'Durasi',
            'price_motor' => 'Harga motor',
            'price_mobil' => 'Harga mobil',
            'description' => 'Keterangan',
            'sort_order' => 'Urutan',
        ]);

        $data['name'] = trim($data['name']);
        $data['description'] = filled($data['description'] ?? null) ? trim($data['description']) : null;
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_popular'] = $request->boolean('is_popular');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'paket';
        $slug = $base;
        $i = 2;

        while (SubscriptionPlan::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}