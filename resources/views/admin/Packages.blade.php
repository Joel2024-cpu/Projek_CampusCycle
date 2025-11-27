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
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger delete-package-btn"
                                        data-id="{{ $package->id }}"
                                        data-nama="{{ $package->nama_paket }}">
                                    <i class="bi bi-trash"></i>
                                </button>
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

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deletePackageModal" tabindex="-1">
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
                Apakah Anda yakin ingin menghapus paket:
                <br>
                <span id="delete_package_name" class="fw-bold text-danger" style="font-size: 16px;"></span>?
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
                <button id="confirmDeletePackageBtn" class="btn"
                        style="
                        background: #dc3545;
                        color: white;
                        padding: 7px 18px;
                        border-radius: 8px;
                        font-size: 14px;">Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // edit
    const editButtons = document.querySelectorAll(".edit-btn");
    const editForm = document.getElementById("editPackageForm");
    const editModal = new bootstrap.Modal(document.getElementById("editPackageModal"));

    editButtons.forEach(btn => {
        btn.addEventListener("click", function() {
            editForm.action = `/admin/packages/update/${this.dataset.id}`;
            document.getElementById("edit_nama").value = this.dataset.nama;
            document.getElementById("edit_durasi").value = this.dataset.durasi;
            document.getElementById("edit_harga").value = this.dataset.harga;
            editModal.show();
        });
    });

    // konfirmasi hapus
    let deleteUrl = "";
    const deleteModal = new bootstrap.Modal(document.getElementById("deletePackageModal"));

    document.querySelectorAll(".delete-package-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            const id = this.dataset.id;
            const nama = this.dataset.nama;

            document.getElementById("delete_package_name").textContent = nama;

            deleteUrl = `/admin/packages/${id}`;

            deleteModal.show();
        });
    });

    // Tombol konfirmasi hapus
    document.getElementById("confirmDeletePackageBtn").addEventListener("click", function () {

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

@endsection
