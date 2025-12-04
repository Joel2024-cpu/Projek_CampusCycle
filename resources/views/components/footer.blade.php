<footer class="bg-[var(--primary)] text-white mt-20">
    <div class="max-w-7xl mx-auto px-6 py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            <div class="md:col-span-2">
                <div class="flex items-center mb-4">
                    <img src="https://i.pinimg.com/originals/43/25/cf/4325cffcaafcb5832272cc10372708e3.png"
                         alt="CampusCycle Logo" class="w-12 h-12 mr-3">
                    <h3 class="text-2xl font-bold">CampusCycle</h3>
                </div>
                <p class="text-white/80 text-lg leading-relaxed max-w-md">
                    Solusi transportasi ramah lingkungan di Universitas Jember.
                    Sewa sepeda mudah, hemat, dan eco-friendly untuk mobilitas kampus.
                </p>
                <div class="flex space-x-4 mt-6">
                    <a href="https://www.instagram.com/univ_jember/"
                       target="_blank"
                       class="text-white/80 hover:text-white transition-colors hover:scale-110 transform duration-200">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                    <a href="https://x.com/univ_jember/"
                       target="_blank"
                       class="text-white/80 hover:text-white transition-colors hover:scale-110 transform duration-200">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="https://www.facebook.com/unejofficial/"
                       target="_blank"
                       class="text-white/80 hover:text-white transition-colors hover:scale-110 transform duration-200">
                        <i class="fab fa-facebook text-xl"></i>
                    </a>
                </div>
            </div>

            <div>
                <h4 class="text-lg font-semibold mb-6 text-white">Tautan Cepat</h4>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('user.bicycles') }}" class="text-white/80 hover:text-white transition-colors flex items-center group">
                            <i class="fas fa-bicycle w-5 mr-2 group-hover:scale-110 transform duration-200"></i>
                            Sewa Sepeda
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.history') }}" class="text-white/80 hover:text-white transition-colors flex items-center group">
                            <i class="fas fa-history w-5 mr-2 group-hover:scale-110 transform duration-200"></i>
                            Riwayat Sewa
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}" class="text-white/80 hover:text-white transition-colors flex items-center group">
                            <i class="fas fa-home w-5 mr-2 group-hover:scale-110 transform duration-200"></i>
                            Beranda
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="text-lg font-semibold mb-6 text-white">Kontak Kami</h4>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <i class="fas fa-map-marker-alt mt-1 mr-3 w-5 text-white/80"></i>
                        <div>
                            <p class="font-medium">Alamat</p>
                            <p class="text-white/80 text-sm">Kampus UNEJ, Jl. Kalimantan No.37,<br>Jember 68121</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-phone mr-3 w-5 text-white/80"></i>
                        <div>
                            <p class="font-medium">Telepon</p>
                            <p class="text-white/80 text-sm">(0331) 330-224</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-envelope mr-3 w-5 text-white/80"></i>
                        <div>
                            <p class="font-medium">Email</p>
                            <p class="text-white/80 text-sm">info@campuscycle.id</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-white/20 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-4 md:mb-0">
                    <p class="text-white/80 text-sm">
                        &copy; 2025 <span class="font-semibold">CampusCycle</span>. All rights reserved.
                    </p>
                </div>
                <div class="flex flex-wrap justify-center gap-6 text-sm">
                    <button data-modal-target="contact-modal" data-modal-toggle="contact-modal"
                            class="text-white/80 hover:text-white transition-colors font-medium">
                        <i class="fas fa-headset mr-1"></i> Pusat Bantuan
                    </button>
                </div>
            </div>
        </div>
    </div>
</footer>
