<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusCycle | Buat Akun</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root { --primary: #006837; }
        * { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body>

    <div class="relative flex items-center justify-center min-h-screen w-full
                 bg-cover bg-center py-10"
         style="background-image: url('https://unej.ac.id/wp-content/uploads/2024/09/bgheaderunej2024.webp');">

        <div class="absolute inset-0 bg-black opacity-50"></div>

        <div class="relative z-10 w-full max-w-md p-8 md:p-10
                    bg-white/90 backdrop-blur-sm
                    rounded-2xl shadow-2xl border border-white/20">

            <div class="absolute top-5 left-6">
                <a href="{{ route('home') }}"
                   class="text-sm text-gray-600 hover:text-[var(--primary)] transition-colors"
                   title="Kembali ke Beranda">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>

            <div class="text-center mb-8 pt-8">
                <img src="https://i.pinimg.com/originals/43/25/cf/4325cffcaafcb5832272cc10372708e3.png"
                     alt="CampusCycle Logo" class="w-20 h-20 mx-auto mb-3">
                <h2 class="text-3xl font-bold text-gray-900 mb-1">
                    Buat Akun Baru
                </h2>
                <p class="text-gray-600">
                    Bergabunglah dengan CampusCycle hari ini!
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg" role="alert">
                    <strong class="font-bold">Oops! Terjadi kesalahan:</strong>
                    <ul class="mt-1 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
              <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg" role="alert">
                  <strong class="font-bold">Sukses:</strong>
                  <span class="block sm:inline">{{ session('success') }}</span>
              </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nama Lengkap</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-gray-400">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <input type="text" name="name" id="name"
                               class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg
                                      focus:ring-green-500 focus:border-green-500 block w-full ps-10 p-3"
                               placeholder="Nama Anda" value="{{ old('name') }}" required
                               oninvalid="this.setCustomValidity('Mohon isi nama lengkap Anda')"
                               oninput="this.setCustomValidity('')">
                    </div>
                </div>

                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-gray-400">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <input type="email" name="email" id="email"
                               class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg
                                      focus:ring-green-500 focus:border-green-500 block w-full ps-10 p-3"
                               placeholder="NIM@mail.unej.ac.id" value="{{ old('email') }}" required
                               oninvalid="this.setCustomValidity('Mohon isi alamat email yang valid')"
                               oninput="this.setCustomValidity('')">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Hanya email @mail.unej.ac.id yang diperbolehkan</p>
                </div>

                <div>
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" name="password" id="password"
                               class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg
                                      focus:ring-green-500 focus:border-green-500 block w-full ps-10 p-3"
                               placeholder="••••••••" required
                               oninvalid="this.setCustomValidity('Mohon buat kata sandi Anda')"
                               oninput="this.setCustomValidity('')">
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900">Konfirmasi Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg
                                      focus:ring-green-500 focus:border-green-500 block w-full ps-10 p-3"
                               placeholder="••••••••" required
                               oninvalid="this.setCustomValidity('Mohon ulangi kata sandi Anda')"
                               oninput="this.setCustomValidity('')">
                    </div>
                </div>

                <button type="submit"
                        class="w-full text-white bg-[var(--primary)] hover:bg-green-800 focus:ring-4
                               focus:outline-none focus:ring-green-300 font-medium rounded-lg
                               text-sm px-5 py-3.5 text-center transition duration-300 shadow-lg">
                    <i class="fa-solid fa-user-plus mr-2"></i> Daftar
                </button>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Atau daftar dengan</span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('auth.google') }}"
                       class="w-full flex justify-center items-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors hover:shadow-md">
                        <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Daftar dengan Google
                    </a>
                </div>
            </div>

            <p class="text-sm font-light text-gray-600 text-center pt-4">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-medium text-[var(--primary)] hover:underline">
                    Login di sini
                </a>
            </p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>
