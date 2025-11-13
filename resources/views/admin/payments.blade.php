@extends('layout.admin')

@section('title', 'Data Pembayaran')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-2 text-success">Data Pembayaran</h2>
        <p class="text-muted mb-0">Kelola semua transaksi pembayaran dan denda</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success">
            <i class="bi bi-download me-2"></i>Export Laporan
        </button>
        <button class="btn btn-success">
            <i class="bi bi-printer me-2"></i>Cetak Laporan
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6>Total Pendapatan</h6>
                    <h3 class="text-success">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
                </div>
                <i class="bi bi-cash-coin fs-4 text-success"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6>Total Denda</h6>
                    <h3 class="text-warning">Rp {{ number_format($stats['total_fines'], 0, ',', '.') }}</h3>
                </div>
                <i class="bi bi-exclamation-triangle fs-4 text-warning"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6>Pending</h6>
                    <h3 class="text-info">{{ $stats['pending_payments'] }}</h3>
                </div>
                <i class="bi bi-clock-history fs-4 text-info"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6>Selesai Hari Ini</h6>
                    <h3 class="text-success">{{ $stats['completed_today'] }}</h3>
                </div>
                <i class="bi bi-check-circle fs-4 text-success"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card-custom mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" value="{{ date('Y-m-01') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status Pembayaran</label>
                <select class="form-select">
                    <option value="">Semua Status</option>
                    <option value="belum">Belum Bayar</option>
                    <option value="lunas">Lunas</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-success w-100">Terapkan Filter</button>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Pembayaran -->
<div class="card-custom">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <span><i class="bi bi-cash-coin me-2"></i> Riwayat Pembayaran</span>
        <div class="text-muted small">
            Menampilkan {{ $payments->total() }} pembayaran
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Pengguna</th>
                        <th>Sepeda</th>
                        <th>Tanggal</th>
                        <th>Durasi</th>
                        <th>Biaya Sewa</th>
                        <th>Denda</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td class="fw-semibold text-success">#T{{ $payment->id }}</td>
                        <td>
                            <div>
                                <div class="fw-semibold">{{ $payment->rental->user->name ?? '-' }}</div>
                                <small class="text-muted">{{ $payment->rental->user->email ?? '-' }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $payment->rental->bicycle->merk ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $payment->created_at->format('d M Y') }}</div>
                            <small class="text-muted">{{ $payment->created_at->format('H:i') }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">{{ $payment->rental->duration}}</span>
                        </td>
                        <td class="text-success">
                            Rp {{ number_format(($payment->total ?? 0) - ($payment->rental->denda ?? 0), 0, ',', '.') }}
                        </td>
                        <td class="text-warning">
                            @if(($payment->rental->denda ?? 0) > 0)
                                Rp {{ number_format($payment->rental->denda, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="fw-bold text-success">
                            Rp {{ number_format($payment->total ?? 0, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($payment->status_bayar == 'lunas')
                                <span class="badge bg-success">Lunas</span>
                            @else
                                <span class="badge bg-secondary">Belum Bayar</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-success" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary" title="Cetak Invoice">
                                    <i class="bi bi-receipt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="bi bi-cash-coin fs-1 d-block mb-2"></i>
                            Belum ada data pembayaran
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($payments->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Summary Card -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card-custom">
            <div class="card-header-custom">
                <i class="bi bi-graph-up me-2"></i> Ringkasan Bulan Ini
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="p-3 rounded bg-light">
                            <i class="bi bi-arrow-up-circle text-success fs-4 d-block mb-2"></i>
                            <div class="fw-bold text-success">Rp 8.5JT</div>
                            <small class="text-muted">Pendapatan</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="p-3 rounded bg-light">
                            <i class="bi bi-clock text-warning fs-4 d-block mb-2"></i>
                            <div class="fw-bold text-warning">Rp 250K</div>
                            <small class="text-muted">Denda</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded bg-light">
                            <i class="bi bi-check-circle text-success fs-4 d-block mb-2"></i>
                            <div class="fw-bold text-success">156</div>
                            <small class="text-muted">Transaksi</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded bg-light">
                            <i class="bi bi-people text-info fs-4 d-block mb-2"></i>
                            <div class="fw-bold text-info">45</div>
                            <small class="text-muted">Pengguna Aktif</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-custom">
            <div class="card-header-custom">
                <i class="bi bi-pie-chart me-2"></i> Distribusi Pendapatan
            </div>
            <div class="card-body">
                <div class="text-center py-4">
                    <i class="bi bi-pie-chart-fill text-muted fs-1 d-block mb-2"></i>
                    <p class="text-muted mb-0">Chart distribusi pendapatan</p>
                    <small class="text-muted">(Integrasi dengan Chart.js)</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
