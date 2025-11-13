@extends('layout.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="container mx-auto px-6 py-5">
    <div class="bg-white shadow-md rounded-2xl p-6">
        <h2 class="text-2xl font-bold text-gray-800">Halo, {{ Auth::user()->name }}! 👋</h2>
        <p class="text-gray-500 mb-6">Selamat datang di CampusCycle - Sistem Sewa Sepeda Universitas Jember</p>
        <div class="grid md:grid-cols-2 gap-6 mt-10 mb-10">
            <div class="bg-green-700 text-white rounded-xl p-6 text-center shadow-lg">
                <h4 class="text-4xl font-bold">{{ $activeRentals }}</h4>
                <p class="text-lg mt-2">Penyewaan Aktif</p>
            </div>

            <div class="bg-yellow-400 text-white rounded-xl p-6 text-center shadow-lg">
                <h4 class="text-4xl font-bold">{{ $totalRentals }}</h4>
                <p class="text-lg mt-2">Total Penyewaan</p>
            </div>
        </div>
        
        <div class="flex gap-4 justify-center">
            <a href="{{ route('user.bicycles') }}" 
                class="bg-green-700 hover:bg-green-800 text-white px-5 py-3 rounded-lg font-semibold transition">
                Lihat Sepeda
            </a>
            <a href="{{ route('user.history') }}" 
                class="border border-green-700 text-green-700 px-5 py-3 rounded-lg font-semibold hover:bg-green-700 hover:text-white transition">
                Riwayat Sewa
            </a>
        </div>
    </div>
</div>
@endsection
