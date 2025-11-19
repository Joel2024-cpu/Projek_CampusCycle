@extends('layout.app')

@section('title', 'Form Sewa Sepeda')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-white shadow-xl rounded-2xl p-8 max-w-lg mx-auto border border-gray-100">
        <h2 class="text-2xl font-bold text-center text-green-700 mb-6" style="font-family: 'Poppins', sans-serif;">
            Form Penyewaan Sepeda
        </h2>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4" role="alert">
                <p class="font-bold mb-1">Oops! Gagal memproses:</p>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('user.rent', $bicycle->id) }}" method="POST" id="rentalForm">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Nama Peminjam</label>
                <input type="text" value="{{ Auth::user()->name }}" readonly
                    class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 bg-gray-100 focus:outline-none">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Waktu Mulai</label>
                <input type="datetime-local" name="start_time" id="start_time"
                       value="{{ old('start_time') }}"
                       class="w-full border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:border-green-500 focus:ring-green-500" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Pilih Paket</label>
                <select name="package_id" id="package_id" class="w-full border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    <option value="">-- Pilih Paket --</option>
                    @foreach ($packages as $package)
                        <option value="{{ $package->id }}"
                                data-hours="{{ $package->durasi_jam }}"
                                data-price="{{ $package->harga }}"
                                {{ old('package_id') == $package->id ? 'selected' : '' }}>
                            {{ $package->nama_paket }} - Rp{{ number_format($package->harga, 0, ',', '.') }} ({{ $package->durasi_jam }} jam)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Estimasi Waktu Selesai</label>
                <input type="text" id="end_time" readonly
                       class="w-full border-gray-300 rounded-lg px-3 py-2 bg-gray-100 focus:outline-none">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Total Biaya</label>
                <input type="text" id="total_cost" readonly
                       class="w-full border-gray-300 rounded-lg px-3 py-2 bg-gray-100 focus:outline-none font-bold text-green-700">
            </div>

            <button type="submit"
                    class="bg-green-700 text-white w-full py-3 rounded-lg font-semibold
                           hover:bg-green-800 transition shadow-lg hover:shadow-green-200
                           transform hover:-translate-y-0.5">
                Konfirmasi Sewa
            </button>
        </form>

        <a href="{{ route('user.bicycles') }}" class="block text-center mt-4 text-gray-600 hover:text-green-700 transition">
            ← Kembali ke daftar sepeda
        </a>
    </div>
</div>

<script>
    // Membungkus listener di DOMContentLoaded agar aman
    document.addEventListener("DOMContentLoaded", function() {

        const startTimeInput = document.getElementById('start_time');
        const packageSelect = document.getElementById('package_id');

        // Fungsi ini akan dipanggil oleh KEDUA input
        function updateRentalDetails() {
            const startInput = startTimeInput.value;
            const selected = packageSelect.options[packageSelect.selectedIndex];

            // Cek jika data-hours ada, jika tidak, set durasi ke 0
            const durasi = selected.dataset.hours ? parseFloat(selected.dataset.hours) : 0;
            // Cek jika data-price ada, jika tidak, set harga ke 0
            const harga = selected.dataset.price ? parseInt(selected.dataset.price) : 0;

            // 1. Update Waktu Selesai (HANYA jika waktu mulai DAN durasi sudah diisi)
            if (startInput && durasi > 0) {
                const start = new Date(startInput);
                const end = new Date(start.getTime() + durasi * 60 * 60 * 1000);

                // Format 'id-ID' (Indonesia) agar mudah dibaca
                document.getElementById('end_time').value = end.toLocaleString('id-ID', {
                    year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
                }) + ' WIB';
            } else {
                document.getElementById('end_time').value = ''; // Kosongkan jika belum lengkap
            }

            // 2. Update Total Biaya
            if (harga > 0) {
                document.getElementById('total_cost').value = 'Rp ' + harga.toLocaleString('id-ID');
            } else {
                document.getElementById('total_cost').value = ''; // Kosongkan jika belum pilih paket
            }
        }

        // Pasang listener di KEDUA input
        startTimeInput.addEventListener('change', updateRentalDetails);
        packageSelect.addEventListener('change', updateRentalDetails);

        // (Opsional) Panggil fungsi saat halaman dimuat, untuk mengisi data 'old()'
        updateRentalDetails();
    });
</script>
@endsection
