@extends('layout.admin')

@section('title', 'Edit Profil Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-2 text-success">Edit Profil</h2>
        <p class="text-muted mb-0">Perbarui informasi akun administrator Anda</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            <div class="position-relative" style="height: 150px; background: linear-gradient(45deg, #006837, #00a859);">
                <div class="position-absolute top-50 start-50 translate-middle text-white text-center w-100">
                    <h3 class="fw-bold mb-0">{{ $user->name }}</h3>
                    <small class="opacity-75">Administrator</small>
                </div>
            </div>

            <div class="card-body p-4 pt-0 position-relative">

                <div class="text-center position-absolute top-0 start-50 translate-middle">
                    <div class="position-relative d-inline-block">
                        <div class="rounded-circle border border-4 border-white shadow bg-light d-flex align-items-center justify-content-center overflow-hidden"
                             style="width: 100px; height: 100px;">
                            @if($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" id="admin-avatar-preview" class="w-100 h-100 object-fit-cover">
                            @else
                                <span id="admin-avatar-initial" class="fs-1 fw-bold text-success">{{ substr($user->name, 0, 1) }}</span>
                                <img src="" id="admin-avatar-preview" class="w-100 h-100 object-fit-cover d-none">
                            @endif
                        </div>
                        <button type="button" onclick="document.getElementById('profile_picture').click()"
                                class="btn btn-sm btn-warning position-absolute bottom-0 end-0 rounded-circle shadow"
                                style="width: 32px; height: 32px; padding: 0;">
                            <i class="bi bi-camera-fill text-white"></i>
                        </button>
                    </div>
                </div>

                <div class="mt-5 pt-4">

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <input type="file" name="profile_picture" id="profile_picture" class="d-none" accept="image/*" onchange="previewAdminImage(event)">

                        <div class="row g-3">
                            <div class="col-md-12 mb-2">
                                <h6 class="text-uppercase text-muted fw-bold small border-bottom pb-2">Informasi Dasar</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>

                            <div class="col-md-12 mt-4 mb-2">
                                <h6 class="text-uppercase text-muted fw-bold small border-bottom pb-2">Keamanan (Ganti Password)</h6>
                                <small class="text-muted d-block mb-3">Kosongkan jika tidak ingin mengubah password.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password">
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-save me-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewAdminImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const preview = document.getElementById('admin-avatar-preview');
        const initial = document.getElementById('admin-avatar-initial');
        preview.src = reader.result;
        preview.classList.remove('d-none');
        if(initial) initial.classList.add('d-none');
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection
