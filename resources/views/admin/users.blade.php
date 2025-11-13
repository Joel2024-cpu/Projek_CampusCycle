    @extends('layout.admin')

    @section('title', 'Data Pengguna')

    @section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-2 text-success">Data Pengguna</h2>
            <p class="text-muted mb-0">Kelola semua pengguna terdaftar di sistem CampusCycle</p>
        </div>
        <button class="btn btn-success">
            <i class="bi bi-download me-2"></i>Export Data
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6>Total Pengguna</h6>
                        <h3 class="text-success">{{ $stats['total'] }}</h3>
                    </div>
                    <i class="bi bi-people fs-4 text-success"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6>Aktif</h6>
                        <h3 class="text-success">{{ $stats['active'] }}</h3>
                    </div>
                    <i class="bi bi-activity fs-4 text-success"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6>Baru Hari Ini</h6>
                        <h3 class="text-warning">{{ $stats['new_today'] }}</h3>
                    </div>
                    <i class="bi bi-person-plus fs-4 text-warning"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6>Diblokir</h6>
                        <h3 class="text-danger">{{ $stats['blocked'] }}</h3>
                    </div>
                    <i class="bi bi-person-x fs-4 text-danger"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data Pengguna -->
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
                            <th>Fakultas</th>
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
                                <span class="badge bg-light text-dark">{{ $user->faculty ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $user->rentals_count }}</span> transaksi
                            </td>
                            <td class="fw-semibold text-success">
                                Rp {{ number_format($user->rentals_sum_total_cost, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($user->status == 'active')
                                    <span class="badge bg-success">Aktif</span>
                                @elseif($user->status == 'inactive')
                                    <span class="badge bg-warning">Tidak Aktif</span>
                                @else
                                    <span class="badge bg-danger">Diblokir</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $user->created_at->format('d M Y') }}</small>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-success" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if($user->status == 'active')
                                        <button class="btn btn-sm btn-outline-warning" title="Blokir">
                                            <i class="bi bi-lock"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-outline-success" title="Aktifkan">
                                            <i class="bi bi-unlock"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-people fs-1 d-block mb-2"></i>
                                Belum ada data pengguna
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>
    @endsection
