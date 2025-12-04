<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusCycle | @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #006837;
            --primary-light: #00a859;
            --light-bg: #f8f9fa;
            --card-bg: #ffffff;
            --text-dark: #212529;
        }

        * { font-family: 'Poppins', sans-serif; }
        body { background-color: var(--light-bg); color: var(--text-dark); }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary) 0%, #004d26 100%);
            padding-top: 1.5rem;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-brand {
            color: white;
            font-weight: 700;
            font-size: 1.4rem;
            text-align: center;
            margin-bottom: 1.5rem;
            text-decoration: none;
            display: block;
            letter-spacing: 0.5px;
        }

        .sidebar-profile {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 15px;
            margin: 0 15px 20px 15px;
            display: flex;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav {
            padding: 0 15px;
            flex-grow: 1; 
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            display: flex;
            align-items: center;
            padding: 12px 15px;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 5px;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white !important;
            transform: translateX(3px);
        }

        .nav-link.active {
            background: white;
            color: var(--primary) !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            font-weight: 600;
        }

        .nav-link i {
            font-size: 1.2rem;
            margin-right: 12px;
            width: 24px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 20px 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: auto;
        }

        .main-content {
            padding: 2rem;
            background: var(--light-bg);
            min-height: 100vh;
            flex: 1;
        }

        .card-custom {
            background: var(--card-bg); border: none; border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08); transition: all 0.3s ease;
        }
        .card-header-custom {
            background: var(--primary); color: white; border: none;
            border-radius: 12px 12px 0 0 !important; padding: 1rem 1.5rem; font-weight: 600;
        }
        .stat-card {
            text-align: center; padding: 1.5rem; border-radius: 12px; background: white;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08); border-left: 4px solid var(--primary);
        }
        .table-custom thead { background: var(--primary); color: white; }
        .badge { font-size: 0.75rem; padding: 0.4rem 0.8rem; border-radius: 20px; }
    </style>
</head>
<body>

<div class="d-flex">

    <div class="sidebar col-md-2 d-none d-md-flex">

        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <i class="bi bi-bicycle me-2"></i>CampusCycle
        </a>

        <div class="sidebar-profile">
            <div class="flex-shrink-0">
                @if(Auth::user()->profile_picture)
                    <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}"
                         alt="Admin" class="rounded-circle border border-2 border-white"
                         width="40" height="40" style="object-fit: cover;">
                @else
                    <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center fw-bold"
                         style="width: 40px; height: 40px; font-size: 18px;">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                @endif
            </div>
            <div class="ms-3 overflow-hidden text-white">
                <div class="fw-bold text-truncate" style="font-size: 0.85rem;">{{ Auth::user()->name }}</div>
                <a href="{{ route('admin.profile') }}" class="text-white-50 text-decoration-none small" style="font-size: 0.75rem;">
                    <i class="bi bi-gear-fill me-1"></i> Edit Profil
                </a>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('admin.bicycles') }}" class="nav-link {{ request()->routeIs('admin.bicycles') ? 'active' : '' }}">
                <i class="bi bi-bicycle"></i> Data Sepeda
            </a>
            <a href="{{ route('admin.packages') }}" class="nav-link {{ request()->routeIs('admin.packages') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Manajemen Paket
            </a>
            <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Pengguna
            </a>
            <a href="{{ route('admin.payments') }}" class="nav-link {{ request()->routeIs('admin.payments') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i> Pembayaran
            </a>
            <a href="{{ route('admin.transactions') }}" class="nav-link {{ request()->routeIs('admin.transactions') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Transaksi
            </a>
        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger w-100 d-flex align-items-center justify-content-center py-2 shadow-sm fw-semibold" style="border-radius: 10px;">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <div class="main-content col-md-10 col-12">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@stack('scripts')
</body>
</html>
