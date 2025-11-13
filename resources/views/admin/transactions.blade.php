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
            <div class="stat-card p-3 border rounded shadow-sm">
                <h6 class="text-capitalize">{{ $status }}</h6>
                <h3 class="text-{{ $color }}">{{ $status_counts[$status] ?? 0 }}</h3>
            </div>
        </div>
    @endforeach
</div>

<div class="card-custom">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2"></i> Daftar Transaksi</span>
        <div class="text-muted small">Menampilkan {{ $transactions->total() }} transaksi</div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Pengguna</th>
                    <th>Sepeda</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>Durasi</th>
                    <th>Total</th>
                    <th>Denda</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                <tr>
                    <td>#T{{ $transaction->id }}</td>
                    <td>{{ $transaction->user->name }}</td>
                    <td>{{ $transaction->bicycle->merk ?? '-' }}</td>
                    <td>{{ $transaction->start_time?->format('d M H:i') ?? '-' }}</td>
                    <td>{{ $transaction->end_time?->format('d M H:i') ?? '-' }}</td>
                    <td>{{ $transaction->duration}}</td>
                    <td>Rp {{ number_format($transaction->total_cost, 0, ',', '.') }}</td>
                    <td>
                        @if($transaction->denda > 0)
                            <span class="text-danger">Rp {{ number_format($transaction->denda, 0, ',', '.') }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('admin.transactions.updateStatus', $transaction->id) }}" method="POST" class="d-flex align-items-center gap-2">
                            @csrf
                            <select name="status" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                                @foreach(['pending','berjalan','selesai','batal'] as $status)
                                    <option value="{{ $status }}" {{ $transaction->status == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        <i class="bi bi-clock-history fs-1 d-block mb-2"></i>
                        Belum ada data transaksi
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
