<x-layouts.dashboard title="Edit Paket Harga" subtitle="Ubah detail paket: {{ $plan->name }}">

    <div class="bg-white rounded-2xl border border-[#E4E9EE] p-6 max-w-3xl">
        <form action="{{ route('admin.paket-harga.update', $plan) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.subscription-plans._form')
        </form>
    </div>

</x-layouts.dashboard>