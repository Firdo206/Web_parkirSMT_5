<x-layouts.dashboard title="Tambah Paket Harga" subtitle="Buat paket langganan baru">

    <div class="bg-white rounded-2xl border border-[#E4E9EE] p-6 max-w-3xl">
        <form action="{{ route('admin.paket-harga.store') }}" method="POST">
            @csrf
            @include('admin.subscription-plans._form')
        </form>
    </div>

</x-layouts.dashboard>