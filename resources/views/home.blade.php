@extends('layout.app')

@section('title', 'Beranda - Sewa Sepeda UNEJ')

@section('content')

<section class="relative flex items-center min-h-screen"
    style="background: linear-gradient(rgba(0, 104, 55, 0.9), rgba(0, 0, 0, 0.7)), url('https://maukuliah.ap-south-1.linodeobjects.com/gallery/001025/Gedung%205%20UNEJ-thumbnail.jpg') center/cover no-repeat;">
    <div class="max-w-7xl mx-auto px-6 py-16 grid md:grid-cols-2 items-center gap-12">
        <div>
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-6">
                Transportasi <span class="text-yellow-400">Cerdas</span> & Ramah Lingkungan di UNEJ
            </h1>
            <p class="text-white/90 text-lg mb-8">
                CampusCycle memudahkan penyewaan sepeda kampus secara digital, efisien, dan hemat energi.
                Nikmati kemudahan transportasi ramah lingkungan dengan harga terjangkau.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="/register"
                    class="bg-yellow-400 text-black font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-xl hover:-translate-y-1 transition">
                    <i class="fas fa-bicycle mr-2"></i>Mulai Sekarang
                </a>
                <a href="#fitur"
                    class="border border-white text-white px-6 py-3 rounded-lg hover:bg-white hover:text-[var(--primary)] transition">
                    <i class="fas fa-play-circle mr-2"></i>Pelajari Lebih Lanjut
                </a>
            </div>

            <div class="grid grid-cols-3 text-center mt-10">
                <div>
                    <h3 class="text-yellow-400 text-2xl font-bold mb-1">500+</h3>
                    <p class="text-white/70">Sepeda Tersedia</p>
                </div>
                <div>
                    <h3 class="text-yellow-400 text-2xl font-bold mb-1">2.5K+</h3>
                    <p class="text-white/70">Pengguna Aktif</p>
                </div>
                <div>
                    <h3 class="text-yellow-400 text-2xl font-bold mb-1">98%</h3>
                    <p class="text-white/70">Kepuasan Pengguna</p>
                </div>
            </div>
        </div>
        <div class="text-center">
            <img src="{{ asset('images/pict.png') }}"
                alt="Sepeda" class="max-w-lg mx-auto drop-shadow-2xl floating">
        </div>
    </div>
</section>

<section id="fitur" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6 text-center">
        <h2 class="text-3xl font-extrabold text-[var(--primary)] mb-4 relative inline-block after:content-[''] after:block after:w-20 after:h-1.5 after:bg-gradient-to-r after:from-[var(--primary)] after:to-[var(--primary-light)] after:mx-auto after:mt-3">
            Fitur Unggulan CampusCycle
        </h2>
        <p class="text-gray-600 text-lg mb-12">Kami menyediakan solusi lengkap untuk kebutuhan transportasi ramah lingkungan di kampus UNEJ</p>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 hover:-translate-y-2 transition">
                <div class="w-20 h-20 mx-auto bg-[rgba(0,104,55,0.1)] rounded-full flex items-center justify-center mb-6 hover:scale-110 transition">
                    <i class="fas fa-bicycle text-4xl text-[var(--primary)]"></i>
                </div>
                <h4 class="text-xl font-bold mb-3">Manajemen Sepeda</h4>
                <p class="text-gray-600 mb-4">Kelola sepeda dengan mudah, dari stok hingga kondisi lapangan. Pantau ketersediaan sepeda secara real-time.</p>
                <span class="inline-block bg-[var(--primary)] text-white px-4 py-1 rounded-full text-sm">Real-time Tracking</span>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 hover:-translate-y-2 transition">
                <div class="w-20 h-20 mx-auto bg-[rgba(0,104,55,0.1)] rounded-full flex items-center justify-center mb-6 hover:scale-110 transition">
                    <i class="fas fa-money-bill-wave text-4xl text-[var(--primary)]"></i>
                </div>
                <h4 class="text-xl font-bold mb-3">Konfirmasi Pembayaran</h4>
                <p class="text-gray-600 mb-4">Pembayaran tunai diverifikasi langsung oleh admin untuk memastikan keakuratan dan keamanan transaksi.</p>
                <span class="inline-block bg-[var(--primary)] text-white px-4 py-1 rounded-full text-sm">Verifikasi Instan</span>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 hover:-translate-y-2 transition">
                <div class="w-20 h-20 mx-auto bg-[rgba(0,104,55,0.1)] rounded-full flex items-center justify-center mb-6 hover:scale-110 transition">
                    <i class="fas fa-calculator text-4xl text-[var(--primary)]"></i>
                </div>
                <h4 class="text-xl font-bold mb-3">Denda Otomatis</h4>
                <p class="text-gray-600 mb-4">Sistem otomatis menghitung denda keterlambatan dengan tarif tetap dan transparan.</p>
                <span class="inline-block bg-[var(--primary)] text-white px-4 py-1 rounded-full text-sm">Perhitungan Otomatis</span>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="tentang" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 items-center gap-12">
        <div class="relative">
            <img src="https://maukuliah.ap-south-1.linodeobjects.com/gallery/001025/Gedung%205%20UNEJ-thumbnail.jpg"
                alt="Kampus UNEJ" class="rounded-2xl shadow-lg">
            <div class="absolute bottom-4 left-4">
                <div class="bg-white/90 backdrop-blur-md rounded-xl p-3 shadow-lg flex items-center">
                    <div class="bg-[var(--primary)] rounded-full w-12 h-12 flex items-center justify-center mr-3">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <div>
                        <h5 class="font-bold">2.500+</h5>
                        <p class="text-gray-500 text-sm">Pengguna Terdaftar</p>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <h2 class="text-3xl font-extrabold text-[var(--primary)] mb-4">Tentang CampusCycle</h2>
            <p class="text-gray-600 mb-4 text-lg">CampusCycle adalah layanan penyewaan sepeda yang beroperasi di lingkungan kampus Universitas Jember (UNEJ). Kami berkomitmen untuk menyediakan solusi transportasi yang ramah lingkungan, terjangkau, dan mudah diakses.</p>

            <div class="grid grid-cols-2 gap-3 mb-6 text-gray-700">
                <div>
                    <p class="flex items-center mb-2"><i class="fas fa-leaf text-[var(--primary)] mr-2"></i>Ramah Lingkungan</p>
                    <p class="flex items-center mb-2"><i class="fas fa-bolt text-[var(--primary)] mr-2"></i>Cepat & Efisien</p>
                </div>
                <div>
                    <p class="flex items-center mb-2"><i class="fas fa-shield-alt text-[var(--primary)] mr-2"></i>Aman & Terjamin</p>
                    <p class="flex items-center mb-2"><i class="fas fa-money-bill-wave text-[var(--primary)] mr-2"></i>Terjangkau</p>
                </div>
            </div>

            <a href="#" class="bg-[var(--primary)] text-white px-6 py-3 rounded-lg hover:bg-[var(--primary-light)] transition">
                Pelajari Lebih Lanjut
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-[var(--primary)] text-white text-center">
    <div class="max-w-3xl mx-auto px-6">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Siap Bergabung dengan CampusCycle?</h2>
        <p class="text-lg mb-8">Daftar sekarang dan nikmati kemudahan transportasi ramah lingkungan di kampus UNEJ.</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="/register" class="bg-yellow-400 text-black font-semibold px-8 py-3 rounded-lg shadow-md hover:shadow-xl transition">
                <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
            </a>
            <a href="/login" class="border border-white text-white px-8 py-3 rounded-lg hover:bg-white hover:text-[var(--primary)] transition">
                <i class="fas fa-sign-in-alt mr-2"></i>Masuk
            </a>
        </div>
    </div>
</section>

@endsection
