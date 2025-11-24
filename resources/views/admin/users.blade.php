@extends('layout.admin')

@section('title', 'Data Pengguna')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-2 text-success">Data Pengguna</h2>
        <p class="text-muted mb-0">Kelola semua pengguna terdaftar di sistem CampusCycle</p>
    </div>
    </div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card p-3 border rounded shadow-sm bg-white">
            <div class="d-flex justify-content-between align-items-start">
                <div><h6 class="text-muted small text-uppercase fw-bold">Total Pengguna</h6><h3 class="text-success fw-bold">{{ $stats['total'] }}</h3></div>
                <i class="bi bi-people fs-4 text-success opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card p-3 border rounded shadow-sm bg-white">
            <div class="d-flex justify-content-between align-items-start">
                <div><h6 class="text-muted small text-uppercase fw-bold">Aktif</h6><h3 class="text-success fw-bold">{{ $stats['active'] }}</h3></div>
                <i class="bi bi-activity fs-4 text-success opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card p-3 border rounded shadow-sm bg-white">
            <div class="d-flex justify-content-between align-items-start">
                <div><h6 class="text-muted small text-uppercase fw-bold">Baru Hari Ini</h6><h3 class="text-warning fw-bold">{{ $stats['new_today'] }}</h3></div>
                <i class="bi bi-person-plus fs-4 text-warning opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card p-3 border rounded shadow-sm bg-white">
            <div class="d-flex justify-content-between align-items-start">
                <div><h6 class="text-muted small text-uppercase fw-bold">Diblokir</h6><h3 class="text-danger fw-bold">{{ $stats['blocked'] }}</h3></div>
                <i class="bi bi-person-x fs-4 text-danger opacity-25"></i>
            </div>
        </div>
    </div>
</div>

<div class="card-custom">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-2"></i> Daftar Pengguna</span>
        </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>User ID</th>
                        <th>Profil</th>
                        <th>Email</th>
                        <th>Total Transaksi</th>
                        <th>Total Pengeluaran</th>
                        <th>Status</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="fw-bold text-success">#U{{ $user->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($user->profile_picture)
                                    <img src="{{ asset('storage/' . $user->profile_picture) }}"
                                         class="rounded-circle me-2 border" width="32" height="32" style="object-fit: cover;">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=006837&color=fff"
                                         class="rounded-circle me-2" width="32" height="32">
                                @endif

                                <div class="fw-bold text-dark">{{ $user->name }}</div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $user->rentals_count }} Transaksi</span>
                        </td>
                        <td class="fw-bold text-success">
                            Rp {{ number_format($user->rentals_sum_total_cost, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($user->status == 'active')
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Diblokir</span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ $user->created_at->format('d M Y') }}</small>
                        </td>

                        <td style="min-width: 100px;">
                            @if($user->status == 'active')
                                <form id="form-block-{{ $user->id }}" action="{{ route('admin.users.updateStatus', $user->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="blocked">
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-warning w-100 confirm-action-btn"
                                        title="Blokir User"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmActionModal"
                                        data-message="Apakah Anda yakin ingin memblokir user: <strong>{{ $user->name }}</strong>?"
                                        data-form-id="form-block-{{ $user->id }}">
                                    <i class="bi bi-lock-fill me-1"></i> Blokir
                                </button>
                            @else
                                <form id="form-activate-{{ $user->id }}" action="{{ route('admin.users.updateStatus', $user->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="active">
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-success w-100 confirm-action-btn"
                                        title="Aktifkan User"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmActionModal"
                                        data-message="Apakah Anda yakin ingin mengaktifkan kembali user: <strong>{{ $user->name }}</strong>?"
                                        data-form-id="form-activate-{{ $user->id }}">
                                    <i class="bi bi-unlock-fill me-1"></i> Aktifkan
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-people fs-1 d-block mb-2 opacity-50"></i>
                            Belum ada data pengguna.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} pengguna
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">

                        {{-- Previous Page Link --}}
                        @if ($users->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $users->previousPageUrl() }}" aria-label="Previous">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                            @if ($page == $users->currentPage())
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                {{-- Logika Singkat Halaman --}}
                                @if($page == 1 || $page == $users->lastPage() || ($page >= $users->currentPage() - 1 && $page <= $users->currentPage() + 1))
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @elseif($page == $users->currentPage() - 2 || $page == $users->currentPage() + 2)
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                @endif
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($users->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $users->nextPageUrl() }}" aria-label="Next">
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

<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-labelledby="confirmActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark" id="confirmActionModalLabel">Konfirmasi Tindakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-question-circle text-warning display-4 mb-3 d-block"></i>
                <p id="modal-message-content" class="mb-0 fs-5">Apakah Anda yakin?</p>
            </div>
            <div class="modal-footer justify-content-center border-top-0 pb-4">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary px-4" id="confirm-action-button">Ya, Lakukan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const confirmActionModal = document.getElementById('confirmActionModal');
        let formToSubmitId = null;

        // Listener saat modal akan ditampilkan
        confirmActionModal.addEventListener('show.bs.modal', function (event) {
            // Tombol yang memicu modal
            const button = event.relatedTarget;

            // Ambil data dari atribut data-* di tombol
            const message = button.dataset.message;
            formToSubmitId = button.dataset.formId; // Simpan ID form yang akan di-submit

            // Masukkan pesan konfirmasi ke dalam modal (Support HTML Tag <strong>)
            const modalBody = confirmActionModal.querySelector('.modal-body #modal-message-content');
            modalBody.innerHTML = message;
        });

        // Listener untuk tombol "Ya, Lakukan" di dalam modal
        const confirmButton = document.getElementById('confirm-action-button');
        confirmButton.addEventListener('click', function() {
            if (formToSubmitId) {
                const form = document.getElementById(formToSubmitId);
                if (form) {
                    // Kirim form yang benar
                    form.submit();
                }
            }
        });
    });
</script>

<style>
/* STYLE PAGINATION HIJAU KONSISTEN */
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
@endpush
