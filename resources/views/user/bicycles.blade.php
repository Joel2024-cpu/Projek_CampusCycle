@extends('layout.app')

@section('title', 'Katalog Sepeda')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 py-16 relative overflow-hidden">

    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[500px] bg-green-200/20 rounded-full blur-3xl -z-10 pointer-events-none"></div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">

        <div class="text-center mb-20 relative">

            <div class="absolute top-0 left-0 hidden md:block">
                <a href="{{ route('user.dashboard') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-bold text-gray-500 bg-white border border-gray-200 rounded-full hover:text-[var(--primary)] hover:border-[var(--primary)] transition-all shadow-sm hover:shadow-md">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Dashboard
                </a>
            </div>
            <div class="md:hidden mb-6 flex justify-center">
                <a href="{{ route('user.dashboard') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-bold text-gray-500 bg-white border border-gray-200 rounded-full hover:text-[var(--primary)] transition-all shadow-sm">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Dashboard
                </a>
            </div>

            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm text-xs font-bold text-[var(--primary)] uppercase tracking-widest mb-6">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                CampusCycle
            </span>
            <h1 class="text-5xl md:text-7xl font-black text-gray-900 tracking-tight mb-6 leading-tight">
                Jelajahi <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[var(--primary)] to-teal-400">
                    Kampus Impianmu
                </span>
            </h1>
            <p class="text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">
                Pilih teman perjalanan terbaik. Kami menyediakan sepeda berkualitas tinggi yang dirawat setiap hari untuk kenyamanan maksimal Anda.
            </p>
        </div>

        @if($bicycles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                @foreach($bicycles as $bicycle)
                <div class="group relative bg-white rounded-[2.5rem] p-4 shadow-sm hover:shadow-2xl hover:shadow-green-900/5 transition-all duration-500 flex flex-col h-full border border-transparent hover:border-green-100">

                    <div class="relative bg-gray-100 rounded-[2rem] h-72 flex items-center justify-center overflow-hidden mb-6">

                        <div class="absolute top-4 left-4 z-20">
                            @if($bicycle->available_stock == 0)
                                <div class="px-4 py-2 rounded-full bg-white/80 backdrop-blur-md border border-white/50 text-xs font-bold text-gray-500 flex items-center gap-2 shadow-sm">
                                    <div class="w-2 h-2 rounded-full bg-gray-400"></div> Habis
                                </div>
                            @elseif($bicycle->available_stock <= 2)
                                <div class="px-4 py-2 rounded-full bg-red-500/90 backdrop-blur-md text-xs font-bold text-white flex items-center gap-2 shadow-lg shadow-red-500/30 animate-pulse">
                                    <i class="fa-solid fa-fire"></i> Sisa {{ $bicycle->available_stock }}
                                </div>
                            @else
                                <div class="px-4 py-2 rounded-full bg-white/80 backdrop-blur-md border border-white/50 text-xs font-bold text-[var(--primary)] flex items-center gap-2 shadow-sm">
                                    <div class="w-2 h-2 rounded-full bg-green-500"></div> {{ $bicycle->available_stock }} Tersedia
                                </div>
                            @endif
                        </div>

                        <img src="{{ Str::startsWith($bicycle->image, 'http') ? $bicycle->image : asset('storage/' . $bicycle->image) }}"
                             alt="{{ $bicycle->merk }}"
                             class="w-full h-full object-contain drop-shadow-xl p-6 transform group-hover:scale-110 group-hover:-rotate-2 transition-transform duration-700 ease-out"
                             onerror="this.onerror=null; this.src='{{ asset('images/sepeda.png') }}';">
                    </div>

                    <div class="px-4 pb-4 flex flex-col flex-grow">

                        <div class="mb-4">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 block">
                                {{ $bicycle->type }}
                            </span>
                            <h3 class="text-2xl font-extrabold text-gray-900 group-hover:text-[var(--primary)] transition-colors">
                                {{ $bicycle->merk }}
                            </h3>
                        </div>

                        <p class="text-gray-500 text-sm leading-relaxed line-clamp-2 mb-6">
                            {{ $bicycle->description ?? 'Performa tangguh dan kenyamanan ekstra untuk mobilitas harian Anda di sekitar kampus.' }}
                        </p>

                        <div class="mt-auto pt-6 border-t border-dashed border-gray-200 flex items-center justify-between">

                            @if($bicycle->available_stock > 0)
                                <a href="{{ route('user.rent.form', $bicycle->id) }}"
                                   class="relative overflow-hidden inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-900 text-white shadow-lg group-hover:bg-[var(--primary)] group-hover:w-32 transition-all duration-500 ease-out">
                                    <i class="fa-solid fa-arrow-right absolute group-hover:opacity-0 transition-opacity duration-300"></i>
                                    <span class="absolute opacity-0 group-hover:opacity-100 text-sm font-bold whitespace-nowrap pl-2">Sewa</span>
                                    <i class="fa-solid fa-arrow-right absolute right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-100 text-xs"></i>
                                </a>
                            @else
                                <button disabled class="w-12 h-12 rounded-full bg-gray-100 text-gray-300 flex items-center justify-center cursor-not-allowed">
                                    <i class="fa-solid fa-lock"></i>
                                </button>
                            @endif

                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        @else
            <div class="flex flex-col items-center justify-center py-32 text-center">
                <div class="relative mb-6">
                    <div class="absolute inset-0 bg-green-200 rounded-full blur-2xl opacity-40 animate-pulse"></div>
                    <i class="fa-solid fa-bicycle text-8xl text-[var(--primary)] relative z-10"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Garasi Kosong!</h2>
                <p class="text-gray-500 max-w-md mb-8">Semua unit sedang beroperasi. Silakan kembali lagi nanti untuk memulai petualanganmu.</p>
                <a href="{{ route('user.dashboard') }}" class="px-8 py-3 bg-gray-900 text-white font-bold rounded-full hover:bg-[var(--primary)] transition-colors shadow-lg hover:shadow-xl">
                    Kembali ke Dashboard
                </a>
            </div>
        @endif

    </div>
</div>
@endsection
