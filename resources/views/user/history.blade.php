@extends('layout.app')

@section('title', 'Riwayat Penyewaan')

@section('content')
<div class="min-h-screen py-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl"> <div class="text-center mb-10 bg-white p-8 rounded-2xl shadow-xl">
            <h1 class="text-4xl font-extrabold text-gray-900" style="font-family: 'Poppins', sans-serif;">
                Riwayat Penyewaan Anda
            </h1>
            <p class="text-lg text-gray-500 mt-2">
                Semua transaksi sewa Anda, dari yang terbaru hingga terlama.
            </p>

            <div class="mt-6">
                <a href="{{ route('user.dashboard') }}"
                   class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-[var(--primary)] transition-colors">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>

        @if($rentals->isEmpty())
            <div class="flex flex-col items-center justify-center py-20
                         bg-white rounded-2xl shadow-xl border border-gray-100">
                <div class="p-6 rounded-full mb-4 bg-yellow-50">
                    <i class="fa-solid fa-receipt text-5xl text-yellow-500"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">Belum Ada Riwayat</h3>
                <p class="text-gray-500 max-w-md text-center mt-2 mb-8">
                    Anda belum pernah melakukan penyewaan sepeda.
                    Riwayat akan muncul di sini setelah Anda menyelesaikan sewa pertama.
                </p>
                <a href="{{ route('user.bicycles') }}"
                   class="px-6 py-3 bg-[var(--primary)] text-white font-medium
                          rounded-lg hover:bg-green-800 transition shadow-lg">
                    <i class="fa-solid fa-bicycle mr-2"></i> Mulai Sewa Sekarang
                </a>
            </div>

        @else
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap text-left">

                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr class="text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                <th class="px-6 py-4">Sepeda</th>
                                <th class="px-6 py-4">Paket & Biaya</th>
                                <th class="px-6 py-4">Waktu Mulai</th>
                                <th class="px-6 py-4">Waktu Selesai (Estimasi)</th>
                                <th class="px-6 py-4">Pengembalian Aktual</th>
                                <th class="px-6 py-4">Keterlambatan</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Denda</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($rentals as $rental)
                            @php
                                // DEBUG: Tampilkan data mentah untuk troubleshooting
                                $debugInfo = [
                                    'id' => $rental->id,
                                    'status' => $rental->status,
                                    'return_time' => $rental->return_time,
                                    'denda_db' => $rental->denda,
                                    'end_time' => $rental->end_time,
                                    'start_time' => $rental->start_time
                                ];

                                // PERBAIKAN: Gunakan ABS() untuk menampilkan keterlambatan yang benar
                                $isLate = false;
                                $lateMinutes = 0;
                                $lateDisplay = '-';

                                if ($rental->return_time) {
                                    $endTime = \Carbon\Carbon::parse($rental->end_time);
                                    $returnTime = \Carbon\Carbon::parse($rental->return_time);
                                    $isLate = $returnTime->gt($endTime);

                                    if ($isLate) {
                                        // FIX KRITIS: Memastikan lateMinutes selalu positif
                                        $lateMinutes = abs($returnTime->diffInMinutes($endTime));
                                        $lateHours = floor($lateMinutes / 60);
                                        $lateMinutesRemainder = $lateMinutes % 60;
                                        $lateDisplay = $lateHours . ' jam ' . $lateMinutesRemainder . ' menit';
                                    }
                                }

                                // Gunakan denda dari database
                                $displayDenda = $rental->denda;
                            @endphp

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 bg-green-100 text-[var(--primary)] rounded-full flex items-center justify-center text-xl shadow-sm">
                                            <i class="fa-solid fa-bicycle"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $rental->bicycle->merk ?? 'Sepeda' }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Kode: {{ $rental->bicycle->kode_sepeda ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 font-medium dark:text-white">{{ $rental->package->nama_paket ?? '-' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Rp {{ number_format($rental->total_cost, 0, ',', '.') }}</div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($rental->start_time)->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($rental->start_time)->format('H:i') }} WIB</div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($rental->end_time)->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($rental->end_time)->format('H:i') }} WIB</div>
                                </td>

                                <td class="px-6 py-4">
                                    @if($rental->return_time)
                                        <div class="text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($rental->return_time)->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($rental->return_time)->format('H:i') }} WIB</div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    @if($isLate)
                                        <div class="flex items-center">
                                            <i class="fa-solid fa-clock text-red-500 mr-2"></i>
                                            <div>
                                                <div class="text-sm font-medium text-red-600">{{ $lateDisplay }}</div>
                                                <div class="text-xs text-gray-500">{{ $lateMinutes }} menit</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    @if($rental->status == 'berjalan')
                                        @if($isLate)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-800 border border-orange-200">
                                                <i class="fa-solid fa-clock mr-1.5"></i> Berjalan (Terlambat)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                                <span class="w-2 h-2 mr-2 bg-blue-600 rounded-full animate-pulse"></span>
                                                Sedang Dipakai
                                            </span>
                                        @endif
                                    @elseif($rental->status == 'pending')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            <i class="fa-regular fa-clock mr-1.5"></i> Menunggu
                                        </span>
                                    @elseif($rental->status == 'selesai')
                                        @if($displayDenda > 0)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                                <i class="fa-solid fa-triangle-exclamation mr-1.5"></i> Selesai (Terlambat)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                                                <i class="fa-solid fa-check mr-1.5"></i> Selesai
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800 border border-gray-200">
                                            <i class="fa-solid fa-ban mr-1.5"></i> {{ ucfirst($rental->status) }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    @if ($displayDenda > 0)
                                        <span class="text-sm font-bold text-red-600 dark:text-red-500">
                                            Rp {{ number_format($displayDenda, 0, ',', '.') }}
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ ceil($lateMinutes / 10) }} blok × Rp 5.000
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(method_exists($rentals, 'hasPages') && $rentals->hasPages())
                <div class="p-4 bg-white border-t border-gray-100 dark:bg-gray-800 dark:border-gray-700">
                    {{ $rentals->links() }}
                </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
