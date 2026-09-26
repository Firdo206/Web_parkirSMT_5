@php
    $plan = $plan ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-[#0B1D33] mb-1.5">Nama Paket</label>
        <input type="text" name="name" value="{{ old('name', $plan->name ?? '') }}"
               placeholder="Contoh: Bulanan"
               class="w-full rounded-lg border border-[#E4E9EE] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00C2A8]/40">
        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-[#0B1D33] mb-1.5">Durasi (hari)</label>
        <input type="number" name="duration_days" min="1" value="{{ old('duration_days', $plan->duration_days ?? '') }}"
               placeholder="7 = mingguan, 30 = bulanan, 365 = tahunan"
               class="w-full rounded-lg border border-[#E4E9EE] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00C2A8]/40">
        @error('duration_days') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-[#0B1D33] mb-1.5">Urutan Tampil</label>
        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $plan->sort_order ?? 0) }}"
               class="w-full rounded-lg border border-[#E4E9EE] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00C2A8]/40">
    </div>

    <div>
        <label class="block text-sm font-semibold text-[#0B1D33] mb-1.5">Harga Motor (Rp)</label>
        <input type="number" name="price_motor" min="0" step="0.01" value="{{ old('price_motor', $plan->price_motor ?? '') }}"
               class="w-full rounded-lg border border-[#E4E9EE] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00C2A8]/40">
        @error('price_motor') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-[#0B1D33] mb-1.5">Harga Mobil (Rp)</label>
        <input type="number" name="price_mobil" min="0" step="0.01" value="{{ old('price_mobil', $plan->price_mobil ?? '') }}"
               class="w-full rounded-lg border border-[#E4E9EE] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00C2A8]/40">
        @error('price_mobil') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-[#0B1D33] mb-1.5">Deskripsi Singkat (opsional)</label>
        <textarea name="description" rows="3" placeholder="Contoh: Cocok untuk pengguna harian yang parkir rutin"
                  class="w-full rounded-lg border border-[#E4E9EE] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00C2A8]/40">{{ old('description', $plan->description ?? '') }}</textarea>
    </div>

    <div class="md:col-span-2 flex items-center gap-6 pt-2">
        <label class="flex items-center gap-2 text-sm font-medium text-[#0B1D33]">
            <input type="checkbox" name="is_popular" value="1" {{ old('is_popular', $plan->is_popular ?? false) ? 'checked' : '' }}
                   class="rounded border-[#E4E9EE] text-[#00C2A8] focus:ring-[#00C2A8]/40">
            Tandai sebagai "Rekomendasi"
        </label>
        <label class="flex items-center gap-2 text-sm font-medium text-[#0B1D33]">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $plan->is_active ?? true) ? 'checked' : '' }}
                   class="rounded border-[#E4E9EE] text-[#00C2A8] focus:ring-[#00C2A8]/40">
            Aktifkan paket ini
        </label>
    </div>

</div>

<div class="flex items-center gap-3 mt-8">
    <button type="submit"
            class="bg-[#FFB100] hover:bg-[#e69e00] transition text-[#0B1D33] font-semibold text-sm px-5 py-2.5 rounded-lg">
        Simpan Paket
    </button>
    <a href="{{ route('admin.paket-harga.index') }}"
       class="text-sm font-medium text-[#5B6B7A] hover:text-[#0B1D33] px-5 py-2.5">
        Batal
    </a>
</div>