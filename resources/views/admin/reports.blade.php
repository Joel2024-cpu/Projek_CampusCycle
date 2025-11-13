@extends('layout.admin')

@section('title', 'Laporan & Analytics')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-2 text-success">Laporan & Analytics</h2>
        <p class="text-muted mb-0">Analisis data dan laporan sistem CampusCycle</p>
    </div>
    <div class="d-flex gap-2">
        <select class="form-select w-auto">
            <option>Bulan Ini</option>
            <option>Minggu Ini</option>
            <option>Bulan Lalu</option>
            <option>Kustom</option>
        </select>
        <button class="btn btn-success">
            <i class="bi bi-download me-2"></i>Export Laporan
        </button>
    </div>
</div>

<!-- Summary Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6>Transaksi Bulan Ini</h6>
                    <h3 class="text-success">{{ $reports['monthly_rentals'] }}</h3>
                </div>
                <i class="bi bi-receipt fs-4 text-success"></i>
            </div>
            <div class="mt-2">
                <small class="text-success">+12% dari bulan lalu</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6>Pendapatan Bulan Ini</h6>
                    <h3 class="text-success">Rp {{ number_format($reports['monthly_revenue'], 0, ',', '.') }}</h3>
                </div>
                <i class="bi bi-cash-coin fs-4 text-success"></i>
            </div>
            <div class="mt-2">
                <small class="text-success">+18% dari bulan lalu</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6>Pengguna Aktif</h6>
                    <h3 class="text-success">{{ $reports['active_users'] }}</h3>
                </div>
                <i class="bi bi-people fs-4 text-success"></i>
            </div>
            <div class="mt-2">
                <small class="text-success">+8 user baru</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6>Rata-rata Rating</h6>
                    <h3 class="text-warning">4.7/5</h3>
                </div>
                <i class="bi bi-star fs-4 text-warning"></i>
            </div>
            <div class="mt-2">
                <small class="text-muted">Berdasarkan 156 review</small>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-header-custom">
                <i class="bi bi-bar-chart me-2"></i> Statistik Transaksi Bulan Ini
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="bi bi-bar-chart-line text-muted fs-1 d-block mb-3"></i>
                    <p class="text-muted">Grafik statistik transaksi</p>
                    <small class="text-muted">(Integrasi dengan Chart.js untuk visualisasi data)</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-custom">
            <div class="card-header-custom">
                <i class="bi bi-pie-chart me-2"></i> Distribusi Tipe Sepeda
            </div>
            <div class="card-body">
                <div class="text-center py-4">
                    <i class="bi bi-pie-chart-fill text-muted fs-1 d-block mb-3"></i>
                    <p class="text-muted">Grafik pie distribusi</p>
                    <small class="text-muted">(Integrasi dengan Chart.js)</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Popular Bicycles -->
<div class="card-custom mb-4">
    <div class="card-header-custom">
        <i class="bi bi-trophy me-2"></i> Sepeda Paling Populer
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>Peringkat</th>
                        <th>Nama Sepeda</th>
                        <th>Tipe</th>
                        <th>Total Disewa</th>
                        <th>Pendapatan</th>
                        <th>Rating</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports['popular_bicycles'] as $bicycle)
                    <tr>
                        <td>
                            <span class="badge bg-{{ $loop->iteration == 1 ? 'warning' : ($loop->iteration == 2 ? 'success' : ($loop->iteration == 3 ? 'info' : 'light')) }} text-{{ $loop->iteration <= 3 ? 'white' : 'dark' }}">
                                #{{ $loop->iteration }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://cdn-icons-png.flaticon.com/512/2972/2972185.png"
                                     class="rounded me-2" width="32" height="32">
                                <div class="fw-semibold">{{ $bicycle->name }}</div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">{{ $bicycle->type }}</span>
                        </td>
                        <td class="fw-semibold text-success">{{ $bicycle->rentals_count }}x</td>
                        <td class="fw-bold text-success">
                            Rp {{ number_format($bicycle->rentals_count * $bicycle->price_per_hour, 0, ',', '.') }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-star-fill text-warning me-1"></i>
                                <span class="fw-semibold">4.8</span>
                                <small class="text-muted ms-1">(24)</small>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-bicycle fs-1 d-block mb-2"></i>
                            Belum ada data sepeda
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Additional Reports -->
<div class="row g-4">
    <div class="col-md-6">
        <div class="card-custom">
            <div class="card-header-custom">
                <i class="bi bi-clock-history me-2"></i> Aktivitas Terbaru
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @for($i = 1; $i <= 5; $i++)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold">Transaksi #T{{ $i*100 }} diselesaikan</div>
                                <small class="text-muted">Oleh: User {{ $i }}</small>
                            </div>
                            <small class="text-muted">2{{ $i }} menit lalu</small>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-custom">
            <div class="card-header-custom">
                <i class="bi bi-exclamation-triangle me-2"></i> Peringatan Sistem
            </div>
            <div class="card-body">
                <div class="alert alert-warning mb-3">
                    <i class="bi bi-tools me-2"></i>
                    <strong>3 sepeda</strong> membutuhkan perbaikan
                </div>
                <div class="alert alert-info mb-3">
                    <i class="bi bi-coin me-2"></i>
                    <strong>5 transaksi</strong> pending konfirmasi
                </div>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>
                    Sistem berjalan normal
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
