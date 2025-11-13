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
                                <img src="{{ asset($bicycle->image ?? 'images/sepeda.png') }}" 
                                class="rounded me-2" width="32" height="32">
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

                                <form action="{{ route('admin.bicycles.destroy', $bicycle->id) }}" method="POST" onsubmit="return confirm('Hapus sepeda ini?')" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
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
        <div class="card-footer bg-white border-0">
            {{ $bicycles->links() }}
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
                    <input type="text" name="kode_sepeda" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Merk</label>
                    <input type="text" name="merk" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Tipe</label>
                    <input type="text" name="type" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label>Status</label>
                    <select name="status" class="form-select" required>
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

<script>
document.addEventListener("DOMContentLoaded", function() {
    const editButtons = document.querySelectorAll(".edit-btn");
    const form = document.getElementById("editBicycleForm");
    const modal = new bootstrap.Modal(document.getElementById("editBicycleModal"));

    editButtons.forEach(btn => {
        btn.addEventListener("click", function() {
            const id = this.dataset.id;
            form.action = `/admin/bicycles/update/${id}`;
            document.getElementById("edit_kode").value = this.dataset.kode;
            document.getElementById("edit_merk").value = this.dataset.merk;
            document.getElementById("edit_type").value = this.dataset.type;
            document.getElementById("edit_description").value = this.dataset.description;
            document.getElementById("edit_status").value = this.dataset.status;
            modal.show();
        });
    });
});
</script>
@endsection
