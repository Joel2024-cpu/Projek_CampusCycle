@extends('layout.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-2 text-success">Dashboard Admin</h2>
        <p class="text-muted mb-0">Selamat datang! Kelola sistem CampusCycle dari sini.</p>
    </div>
    <div class="text-muted">
        <i class="bi bi-calendar3 me-2"></i>{{ now()->format('d M Y, H:i') }}
    </div>
</div>

<div class="row g-3 mb-4">
    @php
        $statIcons = [
            'total_bicycles' => 'bi-bicycle',
            'available_bicycles' => 'bi-check-circle',
            'rented_bicycles' => 'bi-clock',
            'total_users' => 'bi-people',
            'total_rentals' => 'bi-receipt',
            'total_revenue' => 'bi-currency-dollar',
            'total_fines' => 'bi-exclamation-triangle'
        ];

        $statTitles = [
            'total_bicycles' => 'Total Sepeda',
            'available_bicycles' => 'Tersedia',
            'rented_bicycles' => 'Disewa',
            'total_users' => 'Total Pengguna',
            'total_rentals' => 'Total Transaksi',
            'total_revenue' => 'Total Pendapatan',
            'total_fines' => 'Total Denda'
        ];
    @endphp

    @foreach ($stats as $key => $value)
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="w-100">
                        <h6 class="text-uppercase">{{ $statTitles[$key] ?? str_replace('_', ' ', $key) }}</h6>
                        <h3 class="text-success">
                            @if(in_array($key, ['total_revenue', 'total_fines']))
                                Rp {{ number_format($value, 0, ',', '.') }}
                            @else
                                {{ $value }}
                            @endif
                        </h3>
                    </div>
                    <div class="icon-wrapper text-success">
                        <i class="bi {{ $statIcons[$key] ?? 'bi-grid' }} fs-4"></i>
                    </div>
                </div>

                @if($key === 'available_bicycles' && $stats['total_bicycles'] > 0)
                    <div class="mt-3">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Ketersediaan</span>
                            <span>{{ number_format(($value / $stats['total_bicycles']) * 100, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ ($value / $stats['total_bicycles']) * 100 }}%"></div>
                        </div>
                    </div>
                @endif

                @if($key === 'total_rentals' && $stats['total_users'] > 0)
                    <div class="mt-2">
                        <small class="text-muted">
                            Rata-rata: {{ number_format($value / $stats['total_users'], 1) }} transaksi/user
                        </small>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4">
    <!-- Recent Transactions -->
    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i> Transaksi Terakhir</span>
                <a href="#" class="btn btn-light btn-sm text-success fw-semibold">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Pengguna</th>
                                <th>Sepeda</th>
                                <th>Tanggal</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_transactions as $trx)
                                <tr>
                                    <td class="fw-semibold text-success">#{{ $trx->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-2"
                                                 style="width: 32px; height: 32px;">
                                                <span class="text-white fw-bold small">
                                                    {{ substr($trx->user->name ?? 'U', 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $trx->user->name ?? '-' }}</div>
                                                <small class="text-muted">{{ $trx->user->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $trx->bicycle->name ?? '-' }}</div>
                                        <small class="text-muted">{{ $trx->bicycle->type ?? 'Standard' }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $trx->created_at->format('d M Y') }}</div>
                                        <small class="text-muted">{{ $trx->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $trx->duration ?? '1' }} jam
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $trx->status == 'selesai' ? 'success' : ($trx->status == 'berjalan' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($trx->status) }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-success">
                                        Rp {{ number_format($trx->total_cost, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        Belum ada transaksi
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats & System Info -->
    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="card-custom mb-4">
            <div class="card-header-custom">
                <i class="bi bi-graph-up me-2"></i> Ringkasan Hari Ini
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="p-3 rounded bg-light">
                            <i class="bi bi-plus-circle text-success fs-4 d-block mb-2"></i>
                            <div class="fw-bold text-success">5</div>
                            <small class="text-muted">Penyewaan Baru</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="p-3 rounded bg-light">
                            <i class="bi bi-person-plus text-success fs-4 d-block mb-2"></i>
                            <div class="fw-bold text-success">3</div>
                            <small class="text-muted">Pengguna Baru</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded bg-light">
                            <i class="bi bi-cash-coin text-success fs-4 d-block mb-2"></i>
                            <div class="fw-bold text-success">Rp 125K</div>
                            <small class="text-muted">Pendapatan</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded bg-light">
                            <i class="bi bi-exclamation-triangle text-warning fs-4 d-block mb-2"></i>
                            <div class="fw-bold text-warning">2</div>
                            <small class="text-muted">Keterlambatan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="card-custom">
            <div class="card-header-custom">
                <i class="bi bi-server me-2"></i> Status Sistem
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold">Server</span>
                        <span class="badge bg-success">Online</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: 95%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold">Database</span>
                        <span class="badge bg-success">Normal</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: 88%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold">Penyewaan Aktif</span>
                        <span class="badge bg-warning">{{ $stats['rented_bicycles'] ?? 0 }}/{{ $stats['total_bicycles'] ?? 0 }}</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-warning" style="width: {{ ($stats['rented_bicycles'] / max($stats['total_bicycles'], 1)) * 100 }}%"></div>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1 text-success"></i>
                        Sistem berjalan normal. Terakhir diperbarui: {{ now()->format('d M Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
