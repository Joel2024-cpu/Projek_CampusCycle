<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusCycle | @yield('title')</title>

    <!-- 1. Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- 2. Flowbite CSS (Dibutuhkan untuk modal) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <!-- 3. FontAwesome & Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">


    <style>
        :root {
            --primary: #006837;
            --primary-light: #00a859;
            --secondary: #FFD200;
        }
        * { font-family: 'Poppins', sans-serif; }

        body {
            /* Latar belakang abu-abu muda yang bersih */
            background-color: #f8f9fa; /* Ini adalah Tailwind 'bg-gray-100' */
            /* Mendorong konten ke bawah navbar (wajib ada) */
            padding-top: 80px;
        }

        .floating { animation: float 3s ease-in-out infinite; }
        @keyframes float {
            0% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0); }
        }

    </style>
</head>

<body class="bg-gray-100">

    <!-- Navbar "fixed" agar padding-top 80px berfungsi -->
    <div class="fixed top-0 left-0 right-0 z-50 bg-white shadow-sm dark:bg-gray-800">
        <x-navbar />
    </div>

    <main class="min-h-screen">
        @yield('content')
    </main>

    <x-footer />

    <!-- ====================================================== -->
    <!-- INI ADALAH PERBAIKANNYA -->
    <!-- ====================================================== -->

    <!-- 1. Flowbite JS (Agar Modal bisa berfungsi) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- 2. @stack('scripts') (Agar Progress Bar bisa berfungsi) -->
    @stack('scripts')

</body>
</html>
