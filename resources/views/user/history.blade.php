@extends('layout.app')

@section('title', 'Riwayat Penyewaan')

@section('content')
<div class="min-h-screen py-12 bg-green-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">

        <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-sm border border-gray-200/60 p-8 mb-8 flex flex-col md:flex-row items-center justify-between">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                    Riwayat Penyewaan
                </h1>
                <p class="text-gray-500 mt-2">
                    Arsip lengkap perjalanan dan transaksi sepeda Anda.
                </p>
            </div>
            <a href="{{ route('user.dashboard') }}"
               class="group inline-flex items-center px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-50 rounded-xl hover:bg-[var(--primary)] hover:text-white transition-all duration-200">
                <i class="fa-solid fa-arrow-left mr-2 transition-transform group-hover:-translate-x-1"></i>
                Kembali ke Dashboard
            </a>
        </div>

        @if($rentals->isEmpty())
            <div class="bg-white rounded-3xl shadow-lg border-2 border-gray-200 p-16 text-center">
                <div class="w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-bicycle text-4xl text-[var(--primary)]"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Petualangan</h3>
                <p class="text-gray-500 max-w-md mx-auto mb-8">
                    Anda belum pernah menyewa sepeda. Yuk, mulai berkeliling kampus sekarang!
                </p>
                <a href="{{ route('user.bicycles') }}"
                   class="inline-flex items-center px-6 py-3 bg-[var(--primary)] text-white font-semibold rounded-xl shadow-lg hover:shadow-green-500/30 hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fa-solid fa-plus mr-2"></i> Sewa Sepeda Baru
                </a>
            </div>

        @else
            <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl border-2 border-gray-200/60 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100/80 backdrop-blur-sm border-b-2 border-gray-200/60 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-5 border-r border-gray-200">Detail Sepeda</th>
                                <th class="px-6 py-5 border-r border-gray-200">Paket Sewa</th>
                                <th class="px-6 py-5 border-r border-gray-200">Durasi Waktu</th>
                                <th class="px-6 py-5 text-center border-r border-gray-200">Status Waktu</th>
                                <th class="px-6 py-5 text-center border-r border-gray-200">Status Transaksi</th>
                                <th class="px-6 py-5 text-right">Total Tagihan</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @foreach($rentals as $rental)
                            @php
                                $startTime = \Carbon\Carbon::parse($rental->start_time);
                                $endTime = \Carbon\Carbon::parse($rental->end_time);
                                $returnTime = $rental->return_time ? \Carbon\Carbon::parse($rental->return_time) : null;

                                $isLate = false;
                                $lateMinutes = 0;
                                $lateDisplay = '-';

                                if ($returnTime && $returnTime->gt($endTime)) {
                                    $isLate = true;
                                    $lateMinutes = $returnTime->diffInMinutes($endTime);
                                    $lateHours = floor($lateMinutes / 60);
                                    $lateMinutesRemainder = $lateMinutes % 60;
                                    $lateDisplay = $lateHours > 0 ? $lateHours . 'j ' . $lateMinutesRemainder . 'm' : $lateMinutes . ' menit';
                                }

                                $dendaKeterlambatan = $rental->denda;
                                $biayaPaket = $rental->package->harga ?? $rental->total_cost;
                                $dendaKerusakan = $rental->total_cost - ($biayaPaket + $dendaKeterlambatan);
                                if ($dendaKerusakan < 0) {
                                    $dendaKerusakan = 0;
                                }
                                $totalDenda = $dendaKeterlambatan + $dendaKerusakan;

                                $totalBiaya = $rental->total_cost;
                            @endphp

                            <tr class="hover:bg-gray-50/80 backdrop-blur-sm transition-colors group">
                                <td class="px-6 py-4 border-r border-gray-200">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-gray-100/80 backdrop-blur-sm flex items-center justify-center text-gray-400 shrink-0 border border-gray-200/60">
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $rental->bicycle->merk ?? 'Unknown' }}</div>
                                            <div class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md inline-block mt-1 border border-gray-200">
                                                {{ $rental->bicycle->kode_sepeda ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 border-r border-gray-200">
                                    <div class="font-semibold text-gray-700">{{ $rental->package->nama_paket ?? '-' }}</div>
                                    <div class="text-sm text-gray-500 mt-0.5">
                                        Rp {{ number_format($biayaPaket, 0, ',', '.') }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 border-r border-gray-200">
                                    <div class="flex flex-col gap-1">
                                        <div class="text-sm text-gray-600 flex items-center">
                                            <i class="fa-regular fa-circle-play w-5 text-green-600"></i>
                                            <span>{{ \Carbon\Carbon::parse($rental->start_time)->format('d M, H:i') }}</span>
                                        </div>
                                        <div class="text-sm text-gray-600 flex items-center">
                                            <i class="fa-regular fa-circle-stop w-5 text-red-500"></i>
                                            <span>
                                                @if($returnTime)
                                                    {{ $returnTime->format('d M, H:i') }}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center border-r border-gray-200">
                                    @if($rental->status == 'batal')
                                        <span class="text-xs font-medium text-gray-400">-</span>
                                    @elseif($isLate)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-red-50 text-red-600 border border-red-200">
                                            <i class="fa-solid fa-clock mr-1.5"></i> {{ $lateDisplay }}
                                        </span>
                                    @elseif($rental->status == 'selesai')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-green-50 text-green-600 border border-green-200">
                                            <i class="fa-solid fa-check mr-1.5"></i> Tepat Waktu
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center border-r border-gray-200">
                                    @if($rental->status == 'berjalan')
                                        <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-300">
                                            Sedang Jalan
                                        </span>
                                    @elseif($rental->status == 'pending')
                                        <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 border border-yellow-300">
                                            Menunggu
                                        </span>
                                    @elseif($rental->status == 'batal')
                                        <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-300">
                                            Dibatalkan
                                        </span>
                                    @elseif($rental->status == 'selesai')
                                        @if($totalDenda > 0)
                                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700 border border-orange-300">
                                                Selesai (Denda)
                                            </span>
                                        @else
                                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-300">
                                                Selesai
                                            </span>
                                        @endif
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    @if($rental->status == 'batal')
                                        <span class="text-sm font-bold text-gray-400 line-through">Rp {{ number_format($biayaPaket, 0, ',', '.') }}</span>
                                        <div class="text-[10px] text-red-500 font-medium mt-1">Refunded</div>
                                    @else
                                        {{-- PERBAIKAN: Tampilkan total biaya yang sama dengan admin --}}
                                        <div class="text-base font-bold text-gray-900">
                                            Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                                        </div>
                                        @if ($totalDenda > 0)
                                            <div class="flex flex-col items-end gap-0.5 mt-1.5">
                                                @if($dendaKeterlambatan > 0)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-red-50 text-red-600 border border-red-200">
                                                        Telat: +{{ number_format($dendaKeterlambatan, 0, ',', '.') }}
                                                    </span>
                                                @endif

                                                @if($dendaKerusakan > 0)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-orange-50 text-orange-600 border border-orange-200">
                                                        Rusak: +{{ number_format($dendaKerusakan, 0, ',', '.') }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(method_exists($rentals, 'hasPages') && $rentals->hasPages())
                <div class="p-4 bg-gray-50 border-t-2 border-gray-200">
                    {{ $rentals->links() }}
                </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
