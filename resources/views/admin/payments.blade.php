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
        <div class="stat-card p-3 border rounded shadow-sm bg-white">
            <h6 class="text-muted small text-uppercase fw-bold">Total Pendapatan</h6>
            <h3 class="text-success fw-bold">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
            <i class="bi bi-cash-stack fs-4 text-success float-end mt-n4 opacity-25"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card p-3 border rounded shadow-sm bg-white">
            <h6 class="text-muted small text-uppercase fw-bold">Total Denda</h6>
            <h3 class="text-warning fw-bold">Rp {{ number_format($stats['total_fines'], 0, ',', '.') }}</h3>
            <i class="bi bi-exclamation-triangle fs-4 text-warning float-end mt-n4 opacity-25"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card p-3 border rounded shadow-sm bg-white">
            <h6 class="text-muted small text-uppercase fw-bold">Pending</h6>
            <h3 class="text-info fw-bold">{{ $stats['pending_payments'] }}</h3>
            <i class="bi bi-clock-history fs-4 text-info float-end mt-n4 opacity-25"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card p-3 border rounded shadow-sm bg-white">
            <h6 class="text-muted small text-uppercase fw-bold">Selesai Hari Ini</h6>
            <h3 class="text-success fw-bold">{{ $stats['completed_today'] }}</h3>
            <i class="bi bi-check-circle fs-4 text-success float-end mt-n4 opacity-25"></i>
        </div>
    </div>
</div>

<form action="{{ route('admin.payments') }}" method="GET">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Status Pembayaran</label>
                    <select name="status_pembayaran" class="form-select">
                        <option value="Semua" {{ request('status_pembayaran') == 'Semua' ? 'selected' : '' }}>Semua Status</option>
                        <option value="lunas" {{ request('status_pembayaran') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="belum" {{ request('status_pembayaran') == 'belum' ? 'selected' : '' }}>Belum Bayar</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-filter"></i></button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="card-custom">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <span><i class="bi bi-receipt me-2"></i> Riwayat Pembayaran</span>
        </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
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
                        <td class="fw-bold text-success">#T{{ $payment->rental_id }}</td>
                        <td>
                            <div class="fw-bold">{{ $payment->rental->user->name ?? '-' }}</div>
                            <small class="text-muted">{{ $payment->rental->user->email ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $payment->rental->bicycle->merk ?? '-' }}</div>
                            <span class="badge bg-light text-dark border">{{ $payment->rental->bicycle->kode_sepeda ?? '-' }}</span>
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
                                <span class="badge bg-warning text-dark">Belum Bayar</span>
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
                                    data-denda="Rp {{ number_format($payment->rental->denda ?? 0, 0, ',', '.') }}"
                                    data-total="Rp {{ number_format($payment->total, 0, ',', '.') }}"
                                    data-status="{{ $payment->status_bayar }}">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak ada data pembayaran yang cocok.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    Menampilkan {{ $payments->firstItem() ?? 0 }} - {{ $payments->lastItem() ?? 0 }} dari {{ $payments->total() }} data
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Page Link --}}
                        @if ($payments->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $payments->previousPageUrl() }}" aria-label="Previous">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($payments->getUrlRange(1, $payments->lastPage()) as $page => $url)
                            @if ($page == $payments->currentPage())
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                @if($page == 1 || $page == $payments->lastPage() || ($page >= $payments->currentPage() - 1 && $page <= $payments->currentPage() + 1))
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @elseif($page == $payments->currentPage() - 2 || $page == $payments->currentPage() + 2)
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                @endif
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($payments->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $payments->nextPageUrl() }}" aria-label="Next">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link"><i class="bi bi-chevron-right"></i></span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="row g-4 mt-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h6 class="mb-0 fw-bold text-success"><i class="bi bi-cash-coin me-2"></i> Ringkasan Bulan Ini ({{ now()->format('F Y') }})</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded bg-light text-center border">
                            <i class="bi bi-arrow-up-circle text-success fs-3 d-block mb-2"></i>
                            <h4 class="fw-bold text-success">Rp {{ number_format($monthly_stats['revenue'], 0, ',', '.') }}</h4>
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Pendapatan</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded bg-light text-center border">
                            <i class="bi bi-exclamation-triangle text-warning fs-3 d-block mb-2"></i>
                            <h4 class="fw-bold text-warning">Rp {{ number_format($monthly_stats['fines'], 0, ',', '.') }}</h4>
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Total Denda</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h6 class="mb-0 fw-bold text-success"><i class="bi bi-pie-chart me-2"></i> Distribusi Pendapatan</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 250px;">
                @if($revenueDistribution->isEmpty())
                    <div class="text-center text-muted">
                        <i class="bi bi-pie-chart fs-1 d-block mb-2 opacity-50"></i>
                        <p class="small">Belum ada data.</p>
                    </div>
                @else
                    <div style="position: relative; height: 200px; width: 100%;">
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
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="detailModalLabel">Detail Pembayaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="text-center text-muted mb-1">ID Transaksi</h6>
                <h4 id="modal-id" class="fw-bold text-success text-center mb-3">#T1</h4>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Status:</strong>
                        <span id="modal-status" class="badge bg-success">Lunas</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between"><strong>Penyewa:</strong> <span id="modal-user"></span></li>
                    <li class="list-group-item d-flex justify-content-between"><strong>Sepeda:</strong> <span id="modal-sepeda"></span></li>
                    <li class="list-group-item d-flex justify-content-between"><strong>Paket:</strong> <span id="modal-paket"></span></li>
                    <li class="list-group-item d-flex justify-content-between"><strong>Tanggal:</strong> <span id="modal-tanggal"></span></li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Biaya Sewa:</strong>
                        <span id="modal-biaya"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Total Denda:</strong>
                        <span id="modal-denda" class="fw-bold text-danger"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between fs-5 bg-light mt-2">
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

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
            const isLunas = button.dataset.status == 'lunas';

            statusBadge.textContent = isLunas ? 'Lunas' : 'Belum Bayar';
            statusBadge.className = isLunas ? 'badge bg-success' : 'badge bg-warning text-dark';
        });

        const ctx = document.getElementById('revenueDistributionChart');
        if (ctx) {
            @if(isset($revenueDistribution) && $revenueDistribution->count() > 0)
                const chartData = @json($revenueDistribution);
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(chartData),
                        datasets: [{
                            data: Object.values(chartData),
                            backgroundColor: ['#006837', '#FFD200', '#00a859', '#343a40'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'right', labels: { boxWidth: 12 } }
                        }
                    }
                });
            @endif
        }
    });
</script>

<style>
.pagination-sm .page-link {
    padding: 0.3rem 0.6rem;
    font-size: 0.875rem;
    color: #198754;
    background-color: #fff;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    margin: 0 2px;
    transition: all 0.2s;
}
.pagination-sm .page-link:hover {
    color: #146c43;
    background-color: #e9ecef;
    border-color: #dee2e6;
}
.pagination .page-item.active .page-link {
    background-color: #198754;
    border-color: #198754;
    color: white;
}
.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #f8f9fa;
    border-color: #dee2e6;
    opacity: 0.6;
}
</style>
@endpush
