<nav class="fixed top-0 left-0 w-full bg-white/95 backdrop-blur-md shadow-lg z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <a href="/" class="text-[var(--primary)] font-bold text-xl flex items-center">
            <i class="fas fa-bicycle mr-2"></i>CampusCycle
        </a>

        <button id="menu-btn" class="lg:hidden text-[var(--primary)] focus:outline-none">
            <i class="fas fa-bars text-2xl"></i>
        </button>

        <ul id="menu" class="hidden lg:flex space-x-6 items-center font-medium text-[var(--primary)]">
            <li><a href="/" class="hover:text-[var(--primary-light)] transition">Beranda</a></li>
        <li><a href="{{ route('user.profile') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
        Edit Profil
        </a>
        </li>
            @auth
                <li>
                    <span class="text-green-600 font-semibold">{{ Auth::user()->name }}</span>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="ml-2 bg-red-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700 transition">
                            Logout
                        </button>
                    </form>
                </li>
            @else
                <li><a href="/login" class="hover:text-[var(--primary-light)] transition">Masuk</a></li>
                <li>
                    <a href="/register"
                       class="ml-2 bg-[var(--primary)] text-white px-5 py-2 rounded-lg font-semibold hover:bg-[var(--primary-light)] transition">
                       Daftar
                    </a>
                </li>
            @endauth
        </ul>
    </div>

    <div id="mobile-menu" class="hidden lg:hidden bg-white shadow-md">
        <ul class="flex flex-col space-y-3 p-4 text-[var(--primary)] font-medium">
            <li><a href="/" class="hover:text-[var(--primary-light)]">Beranda</a></li>

            @auth
                <li><span class="text-green-600 font-semibold">{{ Auth::user()->name }}</span></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="bg-red-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-red-700 transition">
                            Logout
                        </button>
                    </form>
                </li>
            @else
                <li><a href="/login" class="hover:text-[var(--primary-light)]">Masuk</a></li>
                <li>
                    <a href="/register"
                       class="bg-[var(--primary)] text-white px-5 py-2 rounded-lg font-semibold hover:bg-[var(--primary-light)] transition">
                       Daftar
                    </a>
                </li>
            @endauth
        </ul>
    </div>

    <script>
        const btn = document.getElementById('menu-btn');
        const menu = document.getElementById('mobile-menu');
        btn?.addEventListener('click', () => menu.classList.toggle('hidden'));
    </script>
</nav>
