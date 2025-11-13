@extends('layout.app')

@section('title', 'Lihat Sepeda')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-white shadow-md rounded-2xl p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Daftar Sepeda 🚲</h2>

        @if ($bicycles->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-10">
                <img src="{{ asset('images/sepeda.png') }}" alt="Sepeda kosong" class="w-90 h-60 mb-8 opacity-80">
                <p class="text-lg font-medium text-gray-600 mb-4">
                    Wah, sepertinya belum ada sepeda yang tersedia..
                </p>
                <a href="{{ route('user.dashboard') }}" 
                   class="inline-block bg-green-700 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-800 transition">
                   Kembali ke Dashboard
                </a>
            </div>
        @else
            <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($bicycles as $bicycle)
                <div class="border rounded-xl shadow hover:shadow-lg transition p-4 bg-white flex flex-col items-center text-center">
                    <img src="{{ $bicycle->image ? $bicycle->image : asset('images/sepeda.png') }}" 
                         alt="{{ $bicycle->merk }}" 
                         class="w-40 h-40 object-contain mb-4">
                    <h3 class="text-lg font-semibold text-green-700 mb-1">{{ $bicycle->merk }}</h3>
                    <p class="text-gray-600 text-sm mb-1"><strong>Kode:</strong> {{ $bicycle->kode_sepeda }}</p>
                    <p class="text-gray-600 text-sm mb-1"><strong>Tipe:</strong> {{ $bicycle->type ?? '-' }}</p>
                    <p class="text-gray-500 text-sm mb-3">{{ $bicycle->description ?? 'Tidak ada deskripsi.' }}</p>
                    <a href="{{ route('user.rent.form', $bicycle->id) }}" 
                    class="bg-green-700 text-white px-4 py-2 rounded-lg w-full block text-center hover:bg-green-800 transition">
                    Sewa Sekarang
                    </a>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
