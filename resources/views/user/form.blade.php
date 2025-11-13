@extends('layout.app')

@section('title', 'Form Sewa Sepeda')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-white shadow-md rounded-xl p-8 max-w-lg mx-auto">
        <h2 class="text-2xl font-bold text-center text-green-700 mb-6">Form Penyewaan Sepeda</h2>

        <form action="{{ route('user.rent', $bicycle->id) }}" method="POST" id="rentalForm">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Nama Peminjam</label>
                <input type="text" value="{{ Auth::user()->name }}" readonly
                    class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 bg-gray-100">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Waktu Mulai</label>
                <input type="datetime-local" name="start_time" id="start_time" class="w-full border-gray-300 rounded-lg px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Pilih Paket</label>
                <select name="package_id" id="package_id" class="w-full border-gray-300 rounded-lg px-3 py-2" required>
                    <option value="">-- Pilih Paket --</option>
                    @foreach ($packages as $package)
                        <option value="{{ $package->id }}" data-hours="{{ $package->durasi_jam }}" data-price="{{ $package->harga }}">
                            {{ $package->nama_paket }} - Rp{{ number_format($package->harga, 0, ',', '.') }} ({{ $package->durasi_jam }} jam)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Waktu Selesai</label>
                <input type="text" id="end_time" readonly class="w-full border-gray-300 rounded-lg px-3 py-2 bg-gray-100">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Total Biaya</label>
                <input type="text" id="total_cost" readonly class="w-full border-gray-300 rounded-lg px-3 py-2 bg-gray-100">
            </div>

            <button type="submit"
                class="bg-green-700 text-white w-full py-3 rounded-lg font-semibold hover:bg-green-800 transition">
                Konfirmasi Sewa
            </button>
        </form>

        <a href="{{ route('user.bicycles') }}" class="block text-center mt-4 text-gray-600 hover:text-green-700 transition">
            ← Kembali ke daftar sepeda
        </a>
    </div>
</div>

<script>
    document.getElementById('package_id').addEventListener('change', function() {
        const startInput = document.getElementById('start_time').value;
        const selected = this.options[this.selectedIndex];
        const durasi = selected.dataset.hours;
        const harga = selected.dataset.price;

        if (startInput && durasi) {
            const start = new Date(startInput);
            const end = new Date(start.getTime() + durasi * 60 * 60 * 1000);
            document.getElementById('end_time').value = end.toLocaleString('id-ID');
        }

        if (harga) {
            document.getElementById('total_cost').value = 'Rp ' + parseInt(harga).toLocaleString('id-ID');
        }
    });
</script>
@endsection
