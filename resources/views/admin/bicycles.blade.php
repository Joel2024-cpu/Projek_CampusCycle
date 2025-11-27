@extends('layout.admin')

@section('title', 'Data Sepeda')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-2 text-success">Data Sepeda</h2>
        <p class="text-muted mb-0">Kelola semua data sepeda yang tersedia di sistem</p>
    </div>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addBicycleModal">
        <i class="bi bi-plus-circle me-2"></i>Tambah Sepeda
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Total Sepeda</h6>
            <h3 class="text-success">{{ $stats['total'] }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Tersedia</h6>
            <h3 class="text-success">{{ $stats['available'] }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Disewa</h6>
            <h3 class="text-warning">{{ $stats['rented'] }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Perbaikan</h6>
            <h3 class="text-danger">{{ $stats['maintenance'] }}</h3>
        </div>
    </div>
</div>

<div class="card-custom">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <span><i class="bi bi-bicycle me-2"></i> Daftar Sepeda</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Merk</th>
                        <th>Tipe</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Total Disewa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bicycles as $bicycle)
                    <tr>
                        <td class="fw-semibold text-success">{{ $bicycle->kode_sepeda }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('storage/' . $bicycle->image) }}"
                                class="rounded me-2" width="32" height="32" onerror="this.onerror=null; this.src='{{ asset('images/pict.png') }}';">
                                <div class="fw-semibold">{{ $bicycle->merk }}</div>
                            </div>
                        </td>
                        <td>{{ $bicycle->type }}</td>
                        <td>{{ Str::limit($bicycle->description, 40) }}</td>
                        <td>
                            @if($bicycle->status == 'available')
                                <span class="badge bg-success">Tersedia</span>
                            @elseif($bicycle->status == 'rented')
                                <span class="badge bg-warning">Disewa</span>
                            @else
                                <span class="badge bg-danger">Perbaikan</span>
                            @endif
                        </td>
                        <td class="fw-semibold text-success">{{ $bicycle->rentals_count }}x</td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-success edit-btn"
                                        data-id="{{ $bicycle->id }}"
                                        data-kode="{{ $bicycle->kode_sepeda }}"
                                        data-merk="{{ $bicycle->merk }}"
                                        data-type="{{ $bicycle->type }}"
                                        data-description="{{ $bicycle->description }}"
                                        data-status="{{ $bicycle->status }}">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <button type="button"
                                        class="btn btn-sm btn-outline-danger delete-btn"
                                        data-id="{{ $bicycle->id }}"
                                        data-name="{{ $bicycle->merk }} ({{ $bicycle->kode_sepeda }})"
                                        @if($bicycle->status == 'rented') disabled @endif >
                                    <i class="bi bi-trash"></i>
                                </button>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-bicycle fs-1 d-block mb-2"></i>
                            Belum ada data sepeda
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bicycles->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Menampilkan {{ $bicycles->firstItem() ?? 0 }} - {{ $bicycles->lastItem() ?? 0 }} dari {{ $bicycles->total() }} data
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Page Link --}}
                        @if ($bicycles->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="bi bi-chevron-left"></i>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $bicycles->previousPageUrl() }}" aria-label="Previous">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($bicycles->getUrlRange(1, $bicycles->lastPage()) as $page => $url)
                            @if ($page == $bicycles->currentPage())
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
                        @if ($bicycles->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $bicycles->nextPageUrl() }}" aria-label="Next">
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

<div class="modal fade" id="addBicycleModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.bicycles.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Sepeda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Kode Sepeda</label>
                    <input type="text" name="kode_sepeda" class="form-control @error('kode_sepeda') is-invalid @enderror" required value="{{ old('kode_sepeda') }}">
                    @error('kode_sepeda')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label>Merk</label>
                    <input type="text" name="merk" class="form-control @error('merk') is-invalid @enderror" required value="{{ old('merk') }}">
                    @error('merk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label>Tipe</label>
                    <input type="text" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type') }}">
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label>Status</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="available" @if(old('status') == 'available') selected @endif>Tersedia</option>
                        <option value="rented" @if(old('status') == 'rented') selected @endif>Disewa</option>
                        <option value="maintenance" @if(old('status') == 'maintenance') selected @endif>Perbaikan</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label>Gambar</label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editBicycleModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editBicycleForm" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Edit Sepeda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Kode Sepeda</label>
                    <input type="text" name="kode_sepeda" id="edit_kode" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Merk</label>
                    <input type="text" name="merk" id="edit_merk" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Tipe</label>
                    <input type="text" name="type" id="edit_type" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea name="description" id="edit_description" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label>Status</label>
                    <select name="status" id="edit_status" class="form-select" required>
                        <option value="available">Tersedia</option>
                        <option value="rented">Disewa</option>
                        <option value="maintenance">Perbaikan</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Gambar</label>
                    <input type="file" name="image" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Sepeda -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content" style="border-radius: 14px; padding: 6px 4px 20px 4px;">
            <div class="d-flex justify-content-between align-items-center px-3 pt-3 pb-1">
                <h5 class="fw-bold m-0" style="font-size: 18px;">Konfirmasi Tindakan</h5>
                <button class="btn btn-close m-0 p-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="text-center mt-2 mb-3">
                <div style="
                    width: 65px;
                    height: 65px;
                    border-radius: 50%;
                    border: 3px solid #dc3545;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: auto;">
                    <i class="bi bi-exclamation-lg" style="color: #dc3545; font-size: 40px;"></i>
                </div>
            </div>
            <p class="text-center px-4" style="font-size: 15px; margin-bottom: 6px;">
                Apakah Anda yakin ingin menghapus sepeda:
                <br>
                <span id="delete_bike_name" class="fw-bold text-danger" style="font-size: 16px;"></span>?
            </p>
            <div class="d-flex justify-content-center gap-2 mt-3">
                <button class="btn" data-bs-dismiss="modal"
                        style="
                        border: 1px solid #dcdcdc;
                        background: white;
                        color: #444;
                        padding: 7px 18px;
                        border-radius: 8px;
                        font-size: 14px;">Batal
                </button>
                <button id="confirmDeleteBtn"
                        class="btn"
                        style="
                        background: #dc3545;
                        color: white;
                        padding: 7px 18px;
                        border-radius: 8px;
                        font-size: 14px;">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const editButtons = document.querySelectorAll(".edit-btn");
    const form = document.getElementById("editBicycleForm");

    // FIX KRITIS: Re-open modal JIKA terjadi error saat Tambah Sepeda (Mengatasi ParseError)
    @if ($errors->any())
        var addModal = new bootstrap.Modal(document.getElementById('addBicycleModal'));
        addModal.show();
    @endif

    // Logic untuk Modal Edit
    editButtons.forEach(btn => {
        btn.addEventListener("click", function() {
            const id = this.dataset.id;

            // Mengarahkan Action Edit sesuai rute POST update Anda
            form.action = `/admin/bicycles/update/${id}`;
            form.method = 'POST'; // Pastikan form method adalah POST

            // Mengisi data ke form
            document.getElementById("edit_kode").value = this.dataset.kode;
            document.getElementById("edit_merk").value = this.dataset.merk;
            document.getElementById("edit_type").value = this.dataset.type;
            document.getElementById("edit_description").value = this.dataset.description;
            document.getElementById("edit_status").value = this.dataset.status;

            var editModal = new bootstrap.Modal(document.getElementById("editBicycleModal"));
            editModal.show();
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {

    let deleteUrl = "";
    const deleteModal = new bootstrap.Modal(document.getElementById("deleteConfirmModal"));

    document.querySelectorAll(".delete-btn").forEach(btn => {

        btn.addEventListener("click", function () {
            const id = this.dataset.id;
            const name = this.dataset.name;

            // Isi nama sepeda dalam modal
            document.getElementById("delete_bike_name").textContent = name;

            // Set URL delete
            deleteUrl = `/admin/bicycles/${id}`;

            // Tampilkan modal
            deleteModal.show();
        });
    });

    // Tombol "Ya, Hapus"
    document.getElementById("confirmDeleteBtn").addEventListener("click", function () {

        // Buat form delete
        const form = document.createElement("form");
        form.action = deleteUrl;
        form.method = "POST";

        const csrf = document.createElement("input");
        csrf.type = "hidden";
        csrf.name = "_token";
        csrf.value = "{{ csrf_token() }}";

        const method = document.createElement("input");
        method.type = "hidden";
        method.name = "_method";
        method.value = "DELETE";

        form.appendChild(csrf);
        form.appendChild(method);
        document.body.appendChild(form);

        form.submit();
    });

});

</script>

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
