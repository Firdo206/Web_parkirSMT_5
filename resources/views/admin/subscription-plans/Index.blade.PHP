<x-layouts.dashboard title="Paket Harga" subtitle="Kelola paket langganan parkir (mingguan, bulanan, tahunan)">

    <div x-data="pricingModal()">

        @if (session('success'))
            <div class="mb-6 bg-[#00C2A8]/10 border border-[#00C2A8]/30 text-[#0B1D33] text-sm font-medium px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-600 text-sm font-medium px-4 py-3 rounded-xl">
                <p class="font-semibold mb-1">Ada yang perlu diperbaiki:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-['Sora'] font-bold text-lg text-[#0B1D33]">Daftar Paket</h2>
                <p class="text-sm text-[#5B6B7A]">Harga ini yang tampil ke pengguna saat mendaftar langganan.</p>
            </div>
            <button type="button" @click="openCreate()"
                    class="bg-[#FFB100] hover:bg-[#e69e00] transition text-[#0B1D33] font-semibold text-sm px-4 py-2.5 rounded-lg">
                + Tambah Paket
            </button>
        </div>

        <div class="bg-white rounded-2xl border border-[#E4E9EE] overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[#F4F6F8] text-[#5B6B7A] text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left px-6 py-3 font-semibold">Nama Paket</th>
                        <th class="text-left px-6 py-3 font-semibold">Durasi</th>
                        <th class="text-left px-6 py-3 font-semibold">Harga Motor</th>
                        <th class="text-left px-6 py-3 font-semibold">Harga Mobil</th>
                        <th class="text-left px-6 py-3 font-semibold">Status</th>
                        <th class="text-right px-6 py-3 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($plans as $plan)
                        <tr class="border-t border-[#E4E9EE]">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-[#0B1D33]">{{ $plan->name }}</span>
                                    @if ($plan->is_popular)
                                        <span class="text-[10px] font-bold text-[#0B1D33] bg-[#FFB100] px-2 py-0.5 rounded-full">Rekomendasi</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[#5B6B7A]">{{ $plan->duration_label }}</td>
                            <td class="px-6 py-4 text-[#0B1D33] font-medium">Rp {{ number_format($plan->price_motor, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-[#0B1D33] font-medium">Rp {{ number_format($plan->price_mobil, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @if ($plan->is_active)
                                    <span class="text-xs font-semibold text-[#00C2A8] bg-[#00C2A8]/10 px-2.5 py-1 rounded-full">Aktif</span>
                                @else
                                    <span class="text-xs font-semibold text-[#5B6B7A] bg-[#F4F6F8] px-2.5 py-1 rounded-full">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button"
                                            @click="openEdit({{ \Illuminate\Support\Js::from($plan) }})"
                                            class="text-xs font-semibold text-[#0B1D33] border border-[#E4E9EE] hover:bg-[#F4F6F8] px-3 py-1.5 rounded-lg transition">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.paket-harga.destroy', $plan) }}" method="POST"
                                          onsubmit="return confirm('Hapus paket {{ $plan->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-xs font-semibold text-red-500 border border-red-200 hover:bg-red-50 px-3 py-1.5 rounded-lg transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-[#5B6B7A]">
                                Belum ada paket harga. Klik "Tambah Paket" untuk membuat yang pertama.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ============ MODAL CREATE / EDIT ============ --}}
        <div x-show="modalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
             style="display: none;">
            <div @click.outside="modalOpen = false"
                 class="bg-white rounded-2xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">

                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-['Sora'] font-bold text-lg text-[#0B1D33]"
                        x-text="isEdit ? 'Edit Paket' : 'Tambah Paket Baru'"></h3>
                    <button type="button" @click="modalOpen = false" class="text-[#5B6B7A] hover:text-[#0B1D33] text-lg leading-none">✕</button>
                </div>

                <form :action="formAction" method="POST">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    <input type="hidden" name="_target_action" :value="formAction">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-[#0B1D33] mb-1.5">Nama Paket</label>
                            <input type="text" name="name" x-model="form.name" placeholder="Contoh: Bulanan"
                                   class="w-full rounded-lg border border-[#E4E9EE] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00C2A8]/40">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#0B1D33] mb-1.5">Durasi (hari)</label>
                            <input type="number" name="duration_days" min="1" x-model="form.duration_days"
                                   placeholder="7 / 30 / 365"
                                   class="w-full rounded-lg border border-[#E4E9EE] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00C2A8]/40">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#0B1D33] mb-1.5">Urutan Tampil</label>
                            <input type="number" name="sort_order" min="0" x-model="form.sort_order"
                                   class="w-full rounded-lg border border-[#E4E9EE] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00C2A8]/40">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#0B1D33] mb-1.5">Harga Motor (Rp)</label>
                            <input type="number" name="price_motor" min="0" step="0.01" x-model="form.price_motor"
                                   class="w-full rounded-lg border border-[#E4E9EE] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00C2A8]/40">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#0B1D33] mb-1.5">Harga Mobil (Rp)</label>
                            <input type="number" name="price_mobil" min="0" step="0.01" x-model="form.price_mobil"
                                   class="w-full rounded-lg border border-[#E4E9EE] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00C2A8]/40">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-[#0B1D33] mb-1.5">Deskripsi Singkat (opsional)</label>
                            <textarea name="description" rows="2" x-model="form.description"
                                      placeholder="Contoh: Cocok untuk pengguna harian yang parkir rutin"
                                      class="w-full rounded-lg border border-[#E4E9EE] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00C2A8]/40"></textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-[#0B1D33] mb-1.5">Fitur (satu baris = satu fitur, muncul sebagai checklist)</label>
                            <textarea name="features" rows="4" x-model="form.features"
                                      placeholder="Akses parkir tanpa tiket&#10;Notifikasi masuk & keluar&#10;Prioritas verifikasi wajah"
                                      class="w-full rounded-lg border border-[#E4E9EE] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00C2A8]/40"></textarea>
                        </div>

                        <div class="md:col-span-2 flex items-center gap-6 pt-2">
                            <label class="flex items-center gap-2 text-sm font-medium text-[#0B1D33]">
                                <input type="checkbox" name="is_popular" value="1" x-model="form.is_popular"
                                       class="rounded border-[#E4E9EE] text-[#00C2A8] focus:ring-[#00C2A8]/40">
                                Tandai sebagai "Rekomendasi"
                            </label>
                            <label class="flex items-center gap-2 text-sm font-medium text-[#0B1D33]">
                                <input type="checkbox" name="is_active" value="1" x-model="form.is_active"
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
                        <button type="button" @click="modalOpen = false"
                                class="text-sm font-medium text-[#5B6B7A] hover:text-[#0B1D33] px-5 py-2.5">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function pricingModal() {
            return {
                // Kalau ada error validasi, modal otomatis kebuka lagi dengan data yang tadi diisi
                modalOpen: {{ $errors->any() ? 'true' : 'false' }},
                isEdit: {{ old('_method') === 'PUT' ? 'true' : 'false' }},
                formAction: @js(old('_target_action', route('admin.paket-harga.store'))),
                form: {
                    name: @js(old('name', '')),
                    duration_days: @js(old('duration_days', '')),
                    sort_order: @js(old('sort_order', 0)),
                    price_motor: @js(old('price_motor', '')),
                    price_mobil: @js(old('price_mobil', '')),
                    description: @js(old('description', '')),
                    features: @js(old('features', '')),
                    is_popular: {{ old('is_popular') ? 'true' : 'false' }},
                    is_active: {{ old('is_active', true) ? 'true' : 'false' }},
                },

                openCreate() {
                    this.isEdit = false;
                    this.form = {
                        name: '', duration_days: '', sort_order: 0,
                        price_motor: '', price_mobil: '', description: '', features: '',
                        is_popular: false, is_active: true,
                    };
                    this.formAction = '{{ route('admin.paket-harga.store') }}';
                    this.modalOpen = true;
                },

                openEdit(plan) {
                    this.isEdit = true;
                    this.form = {
                        name: plan.name,
                        duration_days: plan.duration_days,
                        sort_order: plan.sort_order,
                        price_motor: plan.price_motor,
                        price_mobil: plan.price_mobil,
                        description: plan.description ?? '',
                        features: plan.features ?? '',
                        is_popular: !!plan.is_popular,
                        is_active: !!plan.is_active,
                    };
                    this.formAction = "{{ route('admin.paket-harga.update', ['paketHarga' => '__ID__']) }}".replace('__ID__', plan.id);
                    this.modalOpen = true;
                },
            }
        }
    </script>

</x-layouts.dashboard>