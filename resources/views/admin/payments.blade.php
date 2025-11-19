@extends('layout.admin')

@section('title', 'Data Pembayaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-2 text-success">Data Pembayaran</h2>
        <p class="text-muted mb-0">Kelola semua transaksi pembayaran dan denda</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Total Pendapatan</h6>
            <h3 class="text-success">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
            <i class="bi bi-cash-stack fs-4 text-success icon-bg"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Total Denda</h6>
            <h3 class="text-warning">Rp {{ number_format($stats['total_fines'], 0, ',', '.') }}</h3>
            <i class="bi bi-exclamation-triangle fs-4 text-warning icon-bg"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Pending</h6>
            <h3 class="text-info">{{ $stats['pending_payments'] }}</h3>
            <i class="bi bi-clock-history fs-4 text-info icon-bg"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Selesai Hari Ini</h6>
            <h3 class="text-success">{{ $stats['completed_today'] }}</h3>
            <i class="bi bi-check-circle fs-4 text-success icon-bg"></i>
        </div>
    </div>
</div>

<form action="{{ route('admin.payments') }}" method="GET">
    <div class="card-custom mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status Pembayaran</label>
                    <select name="status_pembayaran" class="form-select">
                        <option value="Semua" {{ request('status_pembayaran') == 'Semua' ? 'selected' : '' }}>Semua Status</option>
                        <option value="lunas" {{ request('status_pembayaran') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="belum" {{ request('status_pembayaran') == 'belum' ? 'selected' : '' }}>Belum Bayar</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">Filter</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="card-custom">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <span><i class="bi bi-receipt me-2"></i> Riwayat Pembayaran</span>
        <div class="text-muted small">
            Menampilkan {{ $payments->total() }} data
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
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td class="fw-semibold text-success">#T{{ $payment->rental_id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $payment->rental->user->name ?? '-' }}</div>
                            <small class="text-muted">{{ $payment->rental->user->email ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $payment->rental->bicycle->merk ?? '-' }}</div>
                            <small class="text-muted">{{ $payment->rental->bicycle->kode_sepeda ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $payment->created_at->format('d M Y') }}</div>
                            <small class="text-muted">{{ $payment->created_at->format('H:i') }} WIB</small>
                        </td>
                        <td class="fw-bold text-success">Rp {{ number_format($payment->total, 0, ',', '.') }}</td>
                        <td>
                            @if($payment->status_bayar == 'lunas')
                                <span class="badge bg-success">Lunas</span>
                            @else
                                <span class="badge bg-warning">Belum Bayar</span>
                            @endif
                        </td>

                        <td>
                            <button type="button" class="btn btn-sm btn-outline-success view-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#detailModal"
                                    data-id="#T{{ $payment->rental_id }}"
                                    data-user="{{ $payment->rental->user->name ?? '-' }}"
                                    data-sepeda="{{ $payment->rental->bicycle->merk ?? '-' }} ({{ $payment->rental->bicycle->kode_sepeda ?? '-' }})"
                                    data-paket="{{ $payment->rental->package->nama_paket ?? '-' }}"
                                    data-durasi="{{ $payment->rental->package->durasi_jam ?? '?' }} jam"
                                    data-tanggal="{{ $payment->created_at->format('d M Y, H:i') }} WIB"
                                    data-biaya="Rp {{ number_format($payment->total - $payment->denda, 0, ',', '.') }}"
                                    data-denda="Rp {{ number_format($payment->rental->denda, 0, ',', '.') }}"
                                    data-total="Rp {{ number_format($payment->total, 0, ',', '.') }}"
                                    data-status="{{ $payment->status_bayar }}">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak ada data pembayaran yang cocok dengan filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $payments->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<div class="row g-4 mt-4">
    <div class="col-lg-7">
        <div class="card-custom h-100">
            <div class="card-header-custom">
                <i class="bi bi-cash-coin me-2"></i> Ringkasan Bulan Ini ({{ now()->format('F Y') }})
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="p-3 rounded bg-light text-center">
                            <i class="bi bi-arrow-up-circle text-success fs-3 d-block mb-2"></i>
                            <h4 class="fw-bold text-success">Rp {{ number_format($monthly_stats['revenue'], 0, ',', '.') }}</h4>
                            <small class="text-muted">Total Pendapatan Bulan Ini</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded bg-light text-center">
                            <i class="bi bi-exclamation-triangle text-warning fs-3 d-block mb-2"></i>
                            <h4 class="fw-bold text-warning">Rp {{ number_format($monthly_stats['fines'], 0, ',', '.') }}</h4>
                            <small class="text-muted">Total Denda Bulan Ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-custom h-100">
            <div class="card-header-custom">
                <i class="bi bi-pie-chart me-2"></i> Distribusi Pendapatan (per Paket)
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                @if($revenueDistribution->isEmpty())
                    <div class="text-center text-muted">
                        <i class="bi bi-pie-chart fs-1 d-block mb-2"></i>
                        <p>Belum ada data pendapatan untuk ditampilkan.</p>
                    </div>
                @else
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="revenueDistributionChart"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-success" id="detailModalLabel">Detail Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="text-center text-muted mb-1">ID Transaksi</h6>
                <h4 id="modal-id" class="fw-bold text-success text-center mb-3">#T1</h4>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Status Pembayaran:</strong>
                        <span id="modal-status" class="badge bg-success">Lunas</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between"><strong>Nama Pengguna:</strong> <span id="modal-user"></span></li>
                    <li class="list-group-item d-flex justify-content-between"><strong>Sepeda:</strong> <span id="modal-sepeda"></span></li>
                    <li class="list-group-item d-flex justify-content-between"><strong>Paket Sewa:</strong> <span id="modal-paket"></span></li>
                    <li class="list-group-item d-flex justify-content-between"><strong>Tanggal Transaksi:</strong> <span id="modal-tanggal"></span></li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Biaya Sewa (Tarif Dasar):</strong>
                        <span id="modal-biaya"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between"><strong>Denda:</strong> <span id="modal-denda" class="fw-semibold text-danger"></span></li>
                    <li class="list-group-item d-flex justify-content-between fs-5">
                        <strong class="text-success">Total Bayar:</strong>
                        <strong id="modal-total" class="text-success"></strong>
                    </li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Script untuk Modal Detail (Sudah ada)
        const detailModal = document.getElementById('detailModal');
        detailModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            detailModal.querySelector('#modal-id').textContent = button.dataset.id;
            detailModal.querySelector('#modal-user').textContent = button.dataset.user;
            detailModal.querySelector('#modal-sepeda').textContent = button.dataset.sepeda;
            detailModal.querySelector('#modal-paket').textContent = button.dataset.paket;
            detailModal.querySelector('#modal-tanggal').textContent = button.dataset.tanggal;
            detailModal.querySelector('#modal-biaya').textContent = button.dataset.biaya;
            detailModal.querySelector('#modal-denda').textContent = button.dataset.denda;
            detailModal.querySelector('#modal-total').textContent = button.dataset.total;
            const statusBadge = detailModal.querySelector('#modal-status');
            statusBadge.textContent = (button.dataset.status == 'lunas') ? 'Lunas' : 'Belum Bayar';
            if (button.dataset.status == 'lunas') {
                statusBadge.classList.remove('bg-warning');
                statusBadge.classList.add('bg-success');
            } else {
                statusBadge.classList.remove('bg-success');
                statusBadge.classList.add('bg-warning');
            }
        });

        // 2. Script untuk Pie Chart (PERBAIKAN UKURAN)
        const ctx = document.getElementById('revenueDistributionChart');

        @if(isset($revenueDistribution) && $revenueDistribution->count() > 0)
            const chartData = @json($revenueDistribution);
            const labels = Object.keys(chartData);
            const data = Object.values(chartData);

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan',
                        data: data,
                        backgroundColor: [
                            '#006837', // Hijau Tua
                            '#FFD200', // Kuning
                            '#00a859', // Hijau Muda
                            '#343a40'  // Abu-abu
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    // 2. Nonaktifkan aspect ratio agar chart mengisi div
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    return label + ': Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        @endif
    });
</script>
@endpush
