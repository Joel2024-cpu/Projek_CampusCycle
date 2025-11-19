@extends('layout.admin')

@section('title', 'Data Pengguna')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-2 text-success">Data Pengguna</h2>
        <p class="text-muted mb-0">Kelola semua pengguna terdaftar di sistem CampusCycle</p>
    </div>
    <button class="btn btn-success">
        <i class="bi bi-download me-2"></i>Export Data
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div><h6>Total Pengguna</h6><h3 class="text-success">{{ $stats['total'] }}</h3></div>
                <i class="bi bi-people fs-4 text-success"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div><h6>Aktif</h6><h3 class="text-success">{{ $stats['active'] }}</h3></div>
                <i class="bi bi-activity fs-4 text-success"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div><h6>Baru Hari Ini</h6><h3 class="text-warning">{{ $stats['new_today'] }}</h3></div>
                <i class="bi bi-person-plus fs-4 text-warning"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div><h6>Diblokir</h6><h3 class="text-danger">{{ $stats['blocked'] }}</h3></div>
                <i class="bi bi-person-x fs-4 text-danger"></i>
            </div>
        </div>
    </div>
</div>

<div class="card-custom">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-2"></i> Daftar Pengguna</span>
        <div class="text-muted small">
            Menampilkan {{ $users->total() }} pengguna
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
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
                        <td class="fw-semibold text-success">#U{{ $user->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=006837&color=fff"
                                    class="rounded-circle me-2" width="32" height="32">
                                <div>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <small class="text-muted">NIM: {{ $user->nim ?? '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="fw-semibold">{{ $user->rentals_count }}</span> transaksi
                        </td>
                        <td class="fw-semibold text-success">
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

                        <td style="min-width: 60px;">
                            @if($user->status == 'active')
                                <form id="form-block-{{ $user->id }}" action="{{ route('admin.users.updateStatus', $user->id) }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <input type="hidden" name="status" value="blocked">
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-warning w-100 confirm-action-btn"
                                        title="Blokir"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmActionModal"
                                        data-message="Anda yakin ingin memblokir user: {{ $user->name }}?"
                                        data-form-id="form-block-{{ $user->id }}">
                                    <i class="bi bi-lock"></i>
                                </button>
                            @else
                                <form id="form-activate-{{ $user->id }}" action="{{ route('admin.users.updateStatus', $user->id) }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <input type="hidden" name="status" value="active">
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-success w-100 confirm-action-btn"
                                        title="Aktifkan"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmActionModal"
                                        data-message="Anda yakin ingin mengaktifkan user: {{ $user->name }}?"
                                        data-form-id="form-activate-{{ $user->id }}">
                                    <i class="bi bi-unlock"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-people fs-1 d-block mb-2"></i>
                            Belum ada data pengguna
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} pengguna
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Page Link --}}
                        @if ($users->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="bi bi-chevron-left"></i>
                                </span>
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
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
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
                                <span class="page-link">
                                    <i class="bi bi-chevron-right"></i>
                                </span>
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
            <div class="modal-header">
                <h5 class="modal-title text-success" id="confirmActionModalLabel">Konfirmasi Tindakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="modal-message-content">Apakah Anda yakin?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="confirm-action-button">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<style>
.pagination-sm .page-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 6px;
    margin: 0 2px;
    border: 1px solid #dee2e6;
}

.pagination .page-item.active .page-link {
    background-color: #198754;
    border-color: #198754;
    color: white;
}

.pagination .page-link {
    color: #198754;
}

.pagination .page-link:hover {
    color: #146c43;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #f8f9fa;
    border-color: #dee2e6;
}
</style>
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

            // Masukkan pesan konfirmasi ke dalam modal
            const modalBody = confirmActionModal.querySelector('.modal-body #modal-message-content');
            modalBody.textContent = message;
        });

        // Listener untuk tombol "Ya, Lanjutkan" di dalam modal
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
@endpush
