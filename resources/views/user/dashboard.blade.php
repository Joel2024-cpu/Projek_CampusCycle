@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900" style="font-family: 'Poppins', sans-serif;">
                Halo, {{ Auth::user()->name }}! 👋
            </h1>
            <p class="text-gray-500 mt-1 text-lg">
                Selamat datang di CampusCycle.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-2 space-y-8">

            @php
                // Ambil 1 data sewa yang sedang 'berjalan'
                $currentRental = $rentals->where('status', 'berjalan')->first();
            @endphp

            @if($currentRental)
                @php
                    // Logika untuk Progress Bar Waktu
                    $startTime = \Carbon\Carbon::parse($currentRental->start_time);
                    $endTime = \Carbon\Carbon::parse($currentRental->end_time);
                    $now = now();

                    // Hitung waktu tersisa untuk initial load
                    if ($now->lt($endTime)) {
                        $totalDuration = $endTime->diffInSeconds($startTime);
                        $elapsedDuration = $now->diffInSeconds($startTime);
                        $timePercentage = min(100, max(0, ($elapsedDuration / $totalDuration) * 100));

                        // Hitung sisa waktu dalam jam dan menit
                        $remainingTime = $now->diff($endTime);
                        $remainingHours = $remainingTime->h;
                        $remainingMinutes = $remainingTime->i;
                    } else {
                        // Jika waktu sudah habis
                        $timePercentage = 100;
                        $remainingHours = 0;
                        $remainingMinutes = 0;
                    }
                @endphp

                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden dark:bg-gray-800 dark:border-gray-700">
                    <div class="p-5 flex justify-between items-center bg-gray-50 border-b border-gray-100 dark:bg-gray-700 dark:border-gray-600">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                            @if($currentRental->is_late)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                    <i class="fa-solid fa-clock mr-2"></i>
                                    Sedang Dipakai (Terlambat)
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                    <span class="w-2 h-2 mr-2 bg-blue-600 rounded-full animate-pulse"></span>
                                    Sedang Dipakai
                                </span>
                            @endif
                        </h2>
                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">

                        <div class="md:col-span-1 flex justify-center items-start">
                            <img class="rounded-lg shadow-md object-cover w-full h-48"
                                 src="{{ Str::startsWith($currentRental->bicycle->image, 'http') ? $currentRental->bicycle->image : asset('storage/'. $currentRental->bicycle->image) }}"
                                 alt="{{ $currentRental->bicycle->merk }}" />
                        </div>

                        <div class="md:col-span-2 space-y-4">
                            <div>
                                <h4 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $currentRental->bicycle->merk }}</h4>
                                <p class="text-gray-500 dark:text-gray-400">{{ $currentRental->bicycle->type }} ({{ $currentRental->bicycle->kode_sepeda }})</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <div>
                                    <p class="text-xs font-medium text-gray-400 uppercase">Mulai</p>
                                    <p class="text-gray-700 dark:text-gray-300 font-semibold">{{ $startTime->format('d M, H:i') }} WIB</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-400 uppercase">Estimasi Selesai</p>
                                    <p class="text-red-600 dark:text-red-500 font-semibold">{{ $endTime->format('d M, H:i') }} WIB</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-400 uppercase">Biaya</p>
                                    <p class="text-green-700 dark:text-green-500 font-semibold">Rp {{ number_format($currentRental->total_cost, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-400 uppercase">Paket</p>
                                    <p class="text-gray-700 dark:text-gray-300 font-semibold">{{ $currentRental->package->nama_paket }}</p>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex justify-between items-center mb-1">
                                    <p class="text-xs font-medium text-gray-400 uppercase">Sisa Waktu</p>
                                    <p id="countdown-timer" class="text-sm font-semibold {{ $currentRental->is_late ? 'text-orange-600 dark:text-orange-400' : 'text-blue-600 dark:text-blue-400' }}">
                                        @if($now->lt($endTime))
                                            {{ $remainingHours }} jam {{ $remainingMinutes }} menit
                                        @else
                                            Waktu habis
                                        @endif
                                    </p>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                    <div id="time-progress" class="{{ $currentRental->is_late ? 'bg-orange-600' : 'bg-blue-600' }} h-2.5 rounded-full transition-all duration-300"
                                         style="width: {{ $timePercentage }}%"></div>
                                </div>
                                @if($currentRental->is_late)
                                    <p class="text-xs text-orange-600 mt-2 font-medium">
                                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                                        Terlambat: {{ now()->diffInMinutes($endTime) }} menit
                                        (Denda: Rp {{ number_format($currentRental->calculateDenda(), 0, ',', '.') }})
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end p-6 border-t border-gray-200 dark:bg-gray-700/50">
                        <button type="button"
                                data-modal-target="confirmation-modal"
                                data-modal-toggle="confirmation-modal"
                                class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300
                                       font-medium rounded-lg text-sm px-5 py-2.5 text-center
                                       dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                            <i class="fa-solid fa-undo mr-2"></i> Kembalikan Sepeda Ini
                        </button>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const endTime = new Date('{{ $currentRental->end_time }}').getTime();
                        const startTime = new Date('{{ $currentRental->start_time }}').getTime();
                        const countdownElement = document.getElementById('countdown-timer');
                        const progressElement = document.getElementById('time-progress');
                        const isLate = {{ $currentRental->is_late ? 'true' : 'false' }};

                        function updateCountdown() {
                            const now = new Date().getTime();
                            const totalDuration = endTime - startTime;
                            const elapsed = now - startTime;
                            const remaining = endTime - now;

                            // Update progress bar
                            if (totalDuration > 0) {
                                const progressPercentage = Math.min(100, Math.max(0, (elapsed / totalDuration) * 100));
                                progressElement.style.width = progressPercentage + '%';
                            }

                            // Update countdown timer
                            if (remaining > 0) {
                                const hours = Math.floor(remaining / (1000 * 60 * 60));
                                const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
                                const seconds = Math.floor((remaining % (1000 * 60)) / 1000);

                                countdownElement.textContent = hours + ' jam ' + minutes + ' menit ' + seconds + ' detik';

                                // Update setiap detik
                                setTimeout(updateCountdown, 1000);
                            } else {
                                countdownElement.textContent = 'Waktu habis';
                                countdownElement.className = 'text-sm font-semibold text-orange-600 dark:text-orange-400';
                                progressElement.className = 'bg-orange-600 h-2.5 rounded-full';
                            }
                        }

                        // Mulai countdown
                        updateCountdown();
                    });
                </script>

            @else
                <div class="mb-8">
                    <a href="{{ route('user.bicycles') }}"
                       class="block p-8 bg-gradient-to-r from-[var(--primary)] to-green-800
                              border border-gray-200 rounded-2xl shadow-xl hover:shadow-2xl
                              dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700
                              transition-all transform hover:-translate-y-1">
                        <div class="flex justify-between items-center">
                            <div class="w-full md:w-2/3">
                                <h2 class="mb-2 text-4xl font-extrabold tracking-tight text-white">Siap Berkeliling?</h2>
                                <p class="font-normal text-green-100 dark:text-gray-400 text-lg">
                                    Temukan sepeda yang tersedia dan nikmati kemudahan berkeliling kampus Unej.
                                </p>
                            </div>
                            <i class="fa-solid fa-bicycle text-7xl text-white opacity-20 hidden md:block"></i>
                        </div>
                    </a>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden dark:bg-gray-800 dark:border-gray-700">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        Riwayat Selesai (5 Terakhir)
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    @php
                        $history_rentals = $rentals->where('status', 'selesai')->take(5);
                    @endphp
                    @if($history_rentals->isEmpty())
                        <p class="text-sm text-gray-500 text-center py-10 dark:text-gray-400">Belum ada riwayat sewa yang selesai.</p>
                    @else
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Sepeda</th>
                                <th scope="col" class="px-6 py-3">Waktu Mulai</th>
                                <th scope="col" class="px-6 py-3">Waktu Selesai</th>
                                <th scope="col" class="px-6 py-3">Waktu Pengembalian</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3 text-right">Biaya/Denda</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history_rentals as $rental)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $rental->bicycle->merk ?? 'Sepeda' }}
                                    <span class="block text-xs text-gray-500">{{ $rental->package->nama_paket ?? 'Paket' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $rental->start_time->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $rental->end_time->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($rental->return_time)
                                        {{ $rental->return_time->format('d M Y, H:i') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($rental->denda > 0)
                                        <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Selesai (Terlambat)</span>
                                    @else
                                        <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Selesai</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-medium text-gray-800 dark:text-white">Rp {{ number_format($rental->total_cost, 0, ',', '.') }}</span>
                                    @if($rental->denda > 0)
                                        <span class="block text-xs text-red-600">(Denda Rp {{ number_format($rental->denda, 0, ',', '.') }})</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-8">

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 dark:bg-gray-800 dark:border-gray-700">
                <h5 class="mb-4 text-sm font-bold text-gray-400 uppercase tracking-wider dark:text-gray-400">Menu Aksi</h5>
                <div class="space-y-4">

                    @if($currentRental)
                        <div class="p-4 text-center bg-gray-100 rounded-lg border border-gray-200 dark:bg-gray-700 dark:border-gray-600">
                            <i class="fa-solid fa-bicycle text-2xl text-gray-400 mb-2 dark:text-gray-500"></i>
                            <p class="font-semibold text-gray-700 dark:text-gray-300">Sewa Sepeda Baru</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">(Selesaikan sewa aktif Anda dahulu)</p>
                        </div>
                    @else
                        <a href="{{ route('user.bicycles') }}"
                           class="block p-4 text-center bg-green-50 rounded-lg border border-green-200
                                  hover:bg-green-100 hover:border-green-300 transition-all transform hover:scale-[1.02]
                                  dark:bg-green-900/50 dark:border-green-700 dark:hover:bg-green-900">
                            <i class="fa-solid fa-bicycle text-2xl text-[var(--primary)] mb-2 dark:text-green-400"></i>
                            <p class="font-semibold text-lg text-gray-900 dark:text-white">Sewa Sepeda Baru</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Lihat daftar sepeda yang tersedia</p>
                        </a>
                    @endif

                    <a href="{{ route('user.history') }}"
                       class="block p-4 text-center bg-gray-50 rounded-lg border border-gray-200
                              hover:bg-gray-100 hover:border-gray-300 transition-all transform hover:scale-[1.02]
                              dark:bg-gray-700/50 dark:border-gray-600 dark:hover:bg-gray-700">
                        <i class="fa-solid fa-clock-rotate-left text-2xl text-gray-400 mb-2 dark:text-gray-500"></i>
                        <p class="font-semibold text-lg text-gray-900 dark:text-white">Riwayat Penyewaan</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Lihat semua transaksi Anda</p>
                    </a>

                    <button type="button"
                            data-modal-target="contact-modal" data-modal-toggle="contact-modal"
                            class="w-full block p-4 text-center bg-gray-50 rounded-lg border border-gray-200
                                   hover:bg-gray-100 hover:border-gray-300 transition-all transform hover:scale-[1.02]
                                   dark:bg-gray-700/50 dark:border-gray-600 dark:hover:bg-gray-700">
                        <i class="fa-solid fa-headset text-2xl text-gray-400 mb-2 dark:text-gray-500"></i>
                        <p class="font-semibold text-lg text-gray-900 dark:text-white">Pusat Bantuan</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Hubungi admin jika ada kendala</p>
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 dark:bg-gray-800 dark:border-gray-700">
                <h5 class="mb-4 text-sm font-bold text-gray-400 uppercase tracking-wider dark:text-gray-400">Statistik Akun</h5>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-50 text-yellow-500 rounded-xl text-xl">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <span class="ml-4 text-lg font-semibold text-gray-700 dark:text-gray-300">Total Transaksi</span>
                        </div>
                        <span class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalRentals }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="p-3 bg-red-50 text-red-500 rounded-xl text-xl">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </div>
                            <span class="ml-4 text-lg font-semibold text-gray-700 dark:text-gray-300">Total Denda</span>
                        </div>
                        <span class="text-xl font-extrabold text-red-600 dark:text-red-500">Rp {{ number_format($totalDenda, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div> </div> <div id="contact-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Pusat Bantuan Admin
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="contact-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                    <span class="sr-only">Tutup</span>
                </button>
            </div>
            <div class="p-4 md:p-5 space-y-4">
                <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                    Jika Anda mengalami kendala teknis atau masalah pada sepeda, silakan hubungi admin pada jam operasional.
                </p>
                <ul class="space-y-3">
                    <li class="flex items-center text-sm">
                        <i class="fa-brands fa-whatsapp text-gray-400 w-6 text-lg"></i>
                        <span class="ml-3 text-gray-700 font-medium dark:text-gray-300">0812-3456-7890 (WA Admin)</span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fa-regular fa-clock text-gray-400 w-6 text-lg"></i>
                        <span class="ml-3 text-gray-700 font-medium dark:text-gray-300">Senin - Jumat (08.00 - 16.00)</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<form id="returnFormFlowbite" action="{{ route('user.return', $currentRental->id ?? 0) }}" method="POST" class="hidden">
    @csrf
</form>

<div id="confirmation-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="p-6 text-center">
                <i class="fa-solid fa-triangle-exclamation text-5xl text-orange-500 mb-4 floating"></i>
                <h3 class="mb-5 text-xl font-semibold text-gray-800 dark:text-white">Konfirmasi Pengembalian</h3>
                <p class="mb-6 text-gray-500 dark:text-gray-400">
                    Pastikan Anda siap mengembalikan sepeda. Waktu sewa akan dihentikan dan denda (jika ada) akan dihitung.
                </p>

                <button type="submit" form="returnFormFlowbite"
                        class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center dark:bg-red-500 dark:hover:bg-red-600 me-3">
                    Ya, Kembalikan Sekarang
                </button>

                <button data-modal-hide="confirmation-modal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>


@endsection
