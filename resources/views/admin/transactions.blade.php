@extends('layout.admin')

@section('title', 'Data Transaksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-2 text-success">Data Transaksi</h2>
        <p class="text-muted mb-0">Kelola semua transaksi penyewaan sepeda</p>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach(['pending'=>'info','berjalan'=>'warning','selesai'=>'success','batal'=>'danger'] as $status=>$color)
        <div class="col-md-3">
            <div class="stat-card p-3 border rounded shadow-sm bg-white">
                <h6 class="text-capitalize text-muted mb-1">{{ $status }}</h6>
                <h3 class="text-{{ $color }} fw-bold m-0">{{ $status_counts[$status] ?? 0 }}</h3>
            </div>
        </div>
    @endforeach
</div>

<div class="row mb-4">
    <div class="col-md-6 d-flex align-items-end">
        <h5 class="text-muted mb-0">Total Transaksi: <span class="text-success fw-bold">{{ $transactions->total() }}</span></h5>
    </div>
    <div class="col-md-6">
        <form action="{{ route('admin.transactions') }}" method="GET">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="search" class="form-control border-start-0 ps-0"
                       placeholder="Cari ID, Nama Mahasiswa, atau Sepeda..."
                       value="{{ request('search') }}">
                <button class="btn btn-success" type="submit">Cari</button>
            </div>
        </form>
    </div>
</div>

<div class="card-custom">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2"></i> Daftar Transaksi</span>
        </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Pengguna</th>
                    <th>Sepeda</th>
                    <th>Waktu Sewa</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                <tr>
                    <td class="fw-bold text-success">#T{{ $transaction->id }}</td>
                    <td>
                        <div class="fw-bold">{{ $transaction->user->name }}</div>
                        <small class="text-muted">{{ $transaction->user->email }}</small>
                    </td>
                    <td>
                        {{ $transaction->bicycle->merk ?? '-' }}
                        <span class="badge bg-light text-dark border ms-1">{{ $transaction->bicycle->kode_sepeda ?? '' }}</span>
                    </td>
                    <td>
                        <div class="small">Mulai: {{ $transaction->start_time?->format('d M H:i') ?? '-' }}</div>
                        <div class="small {{ $transaction->is_late && $transaction->status != 'selesai' ? 'text-danger fw-bold' : 'text-success' }}">
                            Batas: {{ $transaction->end_time?->format('d M H:i') ?? '-' }}
                        </div>
                    </td>
                    <td>
    @php
        // HITUNG BREAKDOWN DENDA UNTUK ADMIN
        $biayaPaket = $transaction->package->harga ?? $transaction->total_cost;
        $dendaKeterlambatan = $transaction->denda ?? 0;
        $dendaKerusakan = $transaction->total_cost - ($biayaPaket + $dendaKeterlambatan);
        if ($dendaKerusakan < 0) {
            $dendaKerusakan = 0;
        }
        $totalDenda = $dendaKeterlambatan + $dendaKerusakan;
    @endphp

    <div class="fw-bold">Rp {{ number_format($transaction->total_cost, 0, ',', '.') }}</div>

    @if($totalDenda > 0)
        <div class="mt-1">
            @if($dendaKeterlambatan > 0)
                <small class="text-danger d-block">
                    <i class="bi bi-clock-fill me-1"></i>Telat: +{{ number_format($dendaKeterlambatan, 0, ',', '.') }}
                </small>
            @endif
            @if($dendaKerusakan > 0)
                <small class="text-warning d-block">
                    <i class="bi bi-tools me-1"></i>Rusak: +{{ number_format($dendaKerusakan, 0, ',', '.') }}
                </small>
            @endif
        </div>
    @endif
</td>
                    <td>
                        @if($transaction->status == 'pending')
                            <span class="badge bg-warning text-dark">Menunggu</span>
                        @elseif($transaction->status == 'berjalan')
                            <span class="badge bg-primary">Sedang Jalan</span>
                        @elseif($transaction->status == 'selesai')
                            <span class="badge bg-success">Selesai</span>
                        @else
                            <span class="badge bg-secondary">Batal</span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if($transaction->status == 'pending')
                            <button class="btn btn-sm btn-primary btn-start"
                                    data-id="{{ $transaction->id }}"
                                    data-user="{{ $transaction->user->name }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#startModal"
                                    title="Mulai Sewa">
                                <i class="bi bi-play-fill"></i>
                            </button>

                            <button class="btn btn-sm btn-outline-danger btn-cancel"
                                    data-id="{{ $transaction->id }}"
                                    data-user="{{ $transaction->user->name }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#cancelModal"
                                    title="Batalkan">
                                <i class="bi bi-x-lg"></i>
                            </button>

                        @elseif($transaction->status == 'berjalan')
                            <button class="btn btn-sm btn-success btn-return"
                                    data-id="{{ $transaction->id }}"
                                    data-user="{{ $transaction->user->name }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#returnModal">
                                <i class="bi bi-check-lg"></i> Selesai
                            </button>

                        @else
                            <button class="btn btn-sm btn-light border" disabled><i class="bi bi-check2-all"></i></button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Belum ada data transaksi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transactions->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Menampilkan {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }} dari {{ $transactions->total() }} data
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0">
                    {{-- Previous Page Link --}}
                    @if ($transactions->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $transactions->previousPageUrl() }}" aria-label="Previous">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                        @if ($page == $transactions->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            {{-- Logika Penyingkatan Halaman --}}
                            @if($page == 1 || $page == $transactions->lastPage() || ($page >= $transactions->currentPage() - 1 && $page <= $transactions->currentPage() + 1))
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @elseif($page == $transactions->currentPage() - 2 || $page == $transactions->currentPage() + 2)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($transactions->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $transactions->nextPageUrl() }}" aria-label="Next">
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

<div class="modal fade" id="startModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="startForm" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="status" value="berjalan">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-play-circle me-2"></i>Mulai Sewa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="bi bi-bicycle text-primary display-4"></i>
                </div>
                <p class="mb-1">Apakah Anda yakin ingin memulai sewa untuk:</p>
                <h5 class="fw-bold text-dark" id="startUser">Nama User</h5>
                <p class="text-muted small mt-3">Status akan berubah menjadi <strong>Sedang Jalan</strong> dan waktu sewa dimulai sekarang.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary px-4">Ya, Mulai Sewa</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="cancelForm" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="status" value="batal">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Batalkan Pesanan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="mb-1">Anda akan membatalkan pesanan milik:</p>
                <h5 class="fw-bold text-dark" id="cancelUser">Nama User</h5>
                <p class="text-danger small mt-3 fw-bold">Tindakan ini tidak dapat dibatalkan!</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Kembali</button>
                <button type="submit" class="btn btn-danger px-4">Ya, Batalkan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="returnModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="returnForm" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="status" value="selesai">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Konfirmasi Pengembalian</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="mb-3">Selesaikan sewa untuk: <strong id="returnUser" class="text-success"></strong></p>

                <div class="alert alert-light border-start border-4 border-info d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-info-circle-fill text-info me-2 fs-4"></i>
                    <div>
                        Denda keterlambatan akan dihitung <strong>otomatis</strong> oleh sistem.
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-danger">Biaya Kerusakan (Opsional)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-danger text-white">Rp</span>
                        <input type="number" name="denda_kerusakan" class="form-control" placeholder="0" min="0" value="0">
                    </div>
                    <small class="text-muted">Isi jika ada kerusakan fisik (ban bocor, lecet, dll).</small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Konfirmasi Selesai</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // SOLUSI SIMPLE - Pasti work
    const startButtons = document.querySelectorAll(".btn-start");
    const cancelButtons = document.querySelectorAll(".btn-cancel");
    const returnButtons = document.querySelectorAll(".btn-return");

    startButtons.forEach(btn => {
        btn.addEventListener("click", function() {
            const id = this.dataset.id;
            document.getElementById("startForm").action = "/admin/transactions/" + id + "/status";
            document.getElementById("startUser").textContent = this.dataset.user;
        });
    });

    cancelButtons.forEach(btn => {
        btn.addEventListener("click", function() {
            const id = this.dataset.id;
            document.getElementById("cancelForm").action = "/admin/transactions/" + id + "/status";
            document.getElementById("cancelUser").textContent = this.dataset.user;
        });
    });

    returnButtons.forEach(btn => {
        btn.addEventListener("click", function() {
            const id = this.dataset.id;
            document.getElementById("returnForm").action = "/admin/transactions/" + id + "/status";
            document.getElementById("returnUser").textContent = this.dataset.user;
        });
    });
});
</script>

<style>
/* CSS KHUSUS PAGINATION */
.pagination-sm .page-link {
    padding: 0.3rem 0.6rem;
    font-size: 0.875rem;
    color: #198754; /* Hijau */
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
@endsection
