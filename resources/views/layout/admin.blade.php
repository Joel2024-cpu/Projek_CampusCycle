<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusCycle | @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #006837;
            --primary-light: #00a859;
            --light-bg: #f8f9fa;
            --card-bg: #ffffff;
            --text-dark: #212529;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--light-bg);
            color: var(--text-dark);
        }

        /* Sidebar */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary) 0%, #004d26 100%);
            padding-top: 1.5rem;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-brand {
            color: white;
            font-weight: 700;
            font-size: 1.3rem;
            text-align: center;
            margin-bottom: 2rem;
            padding: 0 1rem;
        }

        .sidebar-nav {
            padding: 0 1rem;
        }

        .sidebar-nav a {
            color: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            padding: 12px 16px;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }

        .sidebar-nav a:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateX(5px);
        }

        .sidebar-nav a.active {
            background: white;
            color: var(--primary);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .sidebar-nav i {
            width: 20px;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        /* Main Content */
        .main-content {
            padding: 2rem;
            background: var(--light-bg);
            min-height: 100vh;
        }

        /* Cards */
        .card-custom {
            background: var(--card-bg);
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .card-header-custom {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px 12px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        /* Stats Cards */
        .stat-card {
            text-align: center;
            padding: 1.5rem;
            border-radius: 12px;
            background: white;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--primary);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card h6 {
            color: #6c757d;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .stat-card h3 {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 0;
        }

        /* Table */
        .table-custom {
            background: white;
            color: var(--text-dark);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        }

        .table-custom thead {
            background: var(--primary);
            color: white;
        }

        .table-custom th {
            border: none;
            padding: 1rem;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .table-custom td {
            border-color: #dee2e6;
            padding: 1rem;
            vertical-align: middle;
        }

        .table-custom tbody tr:hover {
            background: rgba(0, 104, 55, 0.05);
        }

        /* Badges */
        .badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 500;
        }

        .bg-success {
            background: var(--primary) !important;
        }

        .bg-warning {
            background: #ffc107 !important;
            color: #000 !important;
        }

        /* Buttons */
        .btn-success {
            background: var(--primary);
            border: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 104, 55, 0.3);
        }

        /* Progress Bars */
        .progress {
            border-radius: 10px;
            background: #e9ecef;
        }

        .progress-bar {
            border-radius: 10px;
            background: var(--primary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }

            .main-content {
                padding: 1rem;
            }

            .stat-card {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar col-md-2">
        <div class="sidebar-brand">
            <i class="bi bi-bicycle me-2"></i>CampusCycle
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('admin.bicycles') }}" class="{{ request()->routeIs('admin.bicycles') ? 'active' : '' }}">
                <i class="bi bi-bicycle"></i> Data Sepeda
            </a>
            <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Pengguna
            </a>
            <a href="{{ route('admin.payments') }}" class="{{ request()->routeIs('admin.payments') ? 'active' : '' }}">
                <i class="bi bi-cash-coin"></i> Pembayaran
            </a>
            <a href="{{ route('admin.transactions') }}" class="{{ request()->routeIs('admin.transactions') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Transaksi
            </a>
            <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i> Laporan
            </a>
            <div class="mt-4 pt-3">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-light w-100 text-primary fw-semibold">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </button>
                </form>
            </div>
        </nav>
    </div>

    <!-- Main content -->
    <div class="main-content col-md-10">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
