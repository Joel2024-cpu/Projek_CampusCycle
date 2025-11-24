@extends('layout.admin')

@section('title', 'Manajemen Paket')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-2 text-success">Manajemen Paket Sewa</h2>
        <p class="text-muted mb-0">Atur harga dan durasi sewa sepeda</p>
    </div>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPackageModal">
        <i class="bi bi-plus-circle me-2"></i>Tambah Paket
    </button>
</div>

<div class="card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nama Paket</th>
                        <th>Durasi</th>
                        <th>Harga</th>
                        <th>Total Transaksi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($packages as $package)
                    <tr>
                        <td class="fw-bold">{{ $package->nama_paket }}</td>
                        <td>{{ $package->durasi_jam }} Jam</td>
                        <td class="text-success fw-bold">Rp {{ number_format($package->harga, 0, ',', '.') }}</td>
                        <td>{{ $package->rentals_count }}x Digunakan</td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-success edit-btn"
                                    data-id="{{ $package->id }}"
                                    data-nama="{{ $package->nama_paket }}"
                                    data-durasi="{{ $package->durasi_jam }}"
                                    data-harga="{{ $package->harga }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.packages.destroy', $package->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus paket ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4">Belum ada paket sewa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addPackageModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.packages.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header"><h5 class="modal-title">Tambah Paket</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Nama Paket</label>
                    <input type="text" name="nama_paket" class="form-control" placeholder="Contoh: Paket Kilat" required>
                </div>
                <div class="mb-3">
                    <label>Durasi (Jam)</label>
                    <input type="number" name="durasi_jam" class="form-control" min="1" required>
                </div>
                <div class="mb-3">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" class="form-control" min="0" required>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-success">Simpan</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="editPackageModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editPackageForm" method="POST" class="modal-content">
            @csrf
            <div class="modal-header"><h5 class="modal-title">Edit Paket</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Nama Paket</label>
                    <input type="text" name="nama_paket" id="edit_nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Durasi (Jam)</label>
                    <input type="number" name="durasi_jam" id="edit_durasi" class="form-control" min="1" required>
                </div>
                <div class="mb-3">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" id="edit_harga" class="form-control" min="0" required>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-success">Simpan Perubahan</button></div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const editButtons = document.querySelectorAll(".edit-btn");
    const form = document.getElementById("editPackageForm");
    const modal = new bootstrap.Modal(document.getElementById("editPackageModal"));

    editButtons.forEach(btn => {
        btn.addEventListener("click", function() {
            form.action = `/admin/packages/update/${this.dataset.id}`;
            document.getElementById("edit_nama").value = this.dataset.nama;
            document.getElementById("edit_durasi").value = this.dataset.durasi;
            document.getElementById("edit_harga").value = this.dataset.harga;
            modal.show();
        });
    });
});
</script>
@endsection
