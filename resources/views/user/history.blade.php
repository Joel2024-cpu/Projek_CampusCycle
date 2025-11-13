@extends('layout.app')

@section('title', 'Riwayat Penyewaan')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-7">

    <h2 class="text-3xl font-bold text-[var(--primary)] mb-8 text-center">
        Riwayat Penyewaan
    </h2>

    @if($rentals->isEmpty())
        <div class="flex flex-col items-center justify-center text-center py-10">
            <img src="{{ asset('images/sepeda.png') }}" alt="Sepeda" alt="Empty history" class="w-90 h-60 mb-12 opacity-80 floating">
            <p class="text-lg font-medium text-gray-600">
                Wah, sepertinya kamu belum pernah melakukan penyewaan..</p>
            <a href="{{ route('user.dashboard') }}" class="inline-block mt-6 bg-green-700 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-800 transition">
                Kembali ke Dashboard</a>
        </div>
    @else
        <div class="overflow-x-auto bg-white shadow-lg rounded-xl p-6">
            <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-[var(--primary)] text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">Kode Sepeda</th>
                        <th class="py-3 px-4 text-left">Paket</th>
                        <th class="py-3 px-4 text-left">Mulai</th>
                        <th class="py-3 px-4 text-left">Selesai</th>
                        <th class="py-3 px-4 text-left">Status</th>
                        <th class="py-3 px-4 text-right">Denda</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rentals as $rental)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="py-3 px-4 font-semibold text-[var(--primary)]">
                                {{ $rental->bicycle->kode_sepeda ?? '-' }}
                            </td>
                            <td class="py-3 px-4">
                                {{ $rental->package->nama_paket ?? '-' }}
                            </td>
                            <td class="py-3 px-4">{{ \Carbon\Carbon::parse($rental->start_time)->format('d M Y H:i') }}</td>
                            <td class="py-3 px-4">{{ \Carbon\Carbon::parse($rental->end_time)->format('d M Y H:i') }}</td>
                            <td class="py-3 px-4">
                                @php
                                    $color = match($rental->status) {
                                        'selesai' => 'text-green-600',
                                        'berjalan' => 'text-blue-600',
                                        'pending', 'waiting' => 'text-yellow-600',
                                        'batal' => 'text-red-500',
                                        default => 'text-gray-600'
                                    };
                                @endphp
                                <span class="font-semibold {{ $color }}">
                                    {{ ucfirst($rental->status) }}
                                </span>
                                @if ($rental->denda > 0)
                                    <div class="text-red-500 text-sm mt-1">
                                        Denda: Rp{{ number_format($rental->denda, 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
