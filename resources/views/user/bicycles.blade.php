@extends('layout.app')

@section('title', 'Daftar Sepeda')

@section('content')
<div class="min-h-screen bg-gray-50 py-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        <!-- 1. Header Halaman -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-gray-900" style="font-family: 'Poppins', sans-serif;">
                Pilih Sepeda Anda
            </h1>
            <p class="text-lg text-gray-500 mt-2">
                Pilih sepeda yang paling sesuai dengan gaya berkeliling Anda hari ini.
            </p>

            <div class="mt-6">
                <a href="{{ route('user.dashboard') }}"
                   class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-[var(--primary)] transition-colors">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>

        <!-- 2. Grid Responsif untuk Kartu Sepeda -->
        @if($bicycles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Mulai Looping Data Sepeda (Sekarang sudah di-grup) -->
                @foreach($bicycles as $bicycle)
                <a href="{{ route('user.rent.form', $bicycle->id) }}"
                   class="group block bg-white rounded-2xl shadow-xl border border-gray-100
                          overflow-hidden transition-all duration-300
                          hover:shadow-2xl hover:-translate-y-2">

                    <!-- Bagian Gambar Kartu -->
                    <div class="relative w-full h-56">
                        <img src="{{ Str::startsWith($bicycle->image, 'http') ? $bicycle->image : asset('storage/' . $bicycle->image) }}"
                             alt="{{ $bicycle->merk }} {{ $bicycle->type }}"
                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">

                        <!-- ================================== -->
                        <!-- BADGE STOK (INI YANG DIPERBARUI) -->
                        <!-- ================================== -->
                        <span class="absolute top-4 left-4 inline-flex items-center
                                   px-3 py-1 rounded-full text-xs font-bold
                                   bg-green-100 text-[var(--primary)] border border-green-200">
                            <i class="fa-solid fa-check mr-1.5"></i>
                            <strong>{{ $bicycle->available_stock }}</strong>&nbsp;Stok Tersedia
                        </span>
                    </div>

                    <!-- Bagian Konten Kartu -->
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">
                            {{ $bicycle->merk }}
                        </h3>
                        <p class="text-sm font-medium text-gray-500 -mt-2 mb-3">
                            Tipe: {{ $bicycle->type }}
                        </p>

                        <p class="text-gray-600 text-sm mb-5 h-16 overflow-hidden">
                            {{ Str::limit($bicycle->description, 100, '...') }}
                        </p>

                        <div class="w-full text-center bg-[var(--primary)] text-white font-semibold
                                    py-3 rounded-lg transition-all duration-300
                                    group-hover:bg-green-800 group-hover:shadow-lg">
                            Sewa Sekarang <i class="fa-solid fa-arrow-right ml-2"></i>
                        </div>
                    </div>
                </a>
                @endforeach
                <!-- Selesai Looping -->

            </div>
        @else
            <!-- Tampilan Jika Sepeda Kosong (Empty State) -->
            <div class="flex flex-col items-center justify-center py-20
                        bg-white rounded-2xl shadow-xl border border-gray-100">
                <div class="p-6 rounded-full mb-4 bg-yellow-50 animate-pulse">
                    <i class="fa-solid fa-store-slash text-5xl text-yellow-500"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">Sepeda Habis Dipesan</h3>
                <p class="text-gray-500 max-w-md text-center mt-2 mb-8">
                    Wah, sepertinya semua sepeda sedang disewa oleh mahasiswa lain.
                    Silakan kembali lagi nanti, ya!
                </p>
                <a href="{{ route('user.dashboard') }}"
                   class="px-6 py-3 bg-gray-800 text-white font-medium
                          rounded-lg hover:bg-gray-900 transition shadow-lg">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Dashboard
                </a>
            </div>
        @endif

    </div>
</div>
@endsection
