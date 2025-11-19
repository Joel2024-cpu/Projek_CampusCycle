<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Bicycle;
use App\Models\Package;
use App\Models\Rental;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Pastikan ini ada

class AdminController extends Controller
{
    /**
     * Menampilkan dashboard admin dengan statistik.
     */
    public function dashboard()
    {
        // 1. STATISTIK UTAMA (ALL-TIME)
        $stats = [
            'total_bicycles' => Bicycle::count(),
            'available_bicycles' => Bicycle::where('status', 'available')->count(),
            'rented_bicycles' => Bicycle::where('status', 'rented')->count(),
            'total_users' => User::where('role', 'user')->count(),
            'total_rentals' => Rental::count(),
            'total_revenue' => Payment::where('status_bayar', 'lunas')->sum('total'),
            'total_fines' => Rental::sum('denda'),
        ];

        // 2. TRANSAKSI TERAKHIR (5 TERATAS)
        // PERBAIKAN: Nama variabel diubah ke '$rentals' agar cocok dengan view
        $rentals = Rental::with(['user', 'bicycle'])
            ->latest()
            ->take(5)
            ->get();

        // 3. STATISTIK HARIAN (Data 'Ringkasan Hari Ini')
        $today_stats = [
            'new_rentals' => Rental::whereDate('created_at', today())->count(),
            'new_users' => User::where('role', 'user')->whereDate('created_at', today())->count(),
            'revenue_today' => Payment::where('status_bayar', 'lunas')->whereDate('updated_at', today())->sum('total'),
            'pending_transactions' => Rental::where('status', 'pending')->count(),
        ];

        // 4. PERBAIKAN: Tambahkan variabel $totalRentals yang juga error sebelumnya
        $totalRentals = $stats['total_rentals'];

        // 5. Kirim semua data ke view
        return view('admin.dashboard', compact('stats', 'rentals', 'today_stats', 'totalRentals'));
    }

    /**
     * Menampilkan halaman manajemen sepeda.
     */
    public function bicycles()
    {
        $bicycles = Bicycle::withCount(['rentals' => function ($query) {
            $query->where('status', 'selesai');
        }])
        ->latest()
        ->paginate(10);

        $stats = [
            'total' => Bicycle::count(),
            'available' => Bicycle::where('status', 'available')->count(),
            'rented' => Bicycle::where('status', 'rented')->count(),
            'maintenance' => Bicycle::where('status', 'maintenance')->count(),
        ];
        return view('admin.bicycles', compact('bicycles', 'stats'));
    }

    /**
     * Menampilkan halaman manajemen pengguna.
     */
    public function users()
    {
        $users = User::where('role', 'user')
            ->withCount(['rentals'])
            ->withSum('rentals', 'total_cost')
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => User::where('role', 'user')->count(),
            'active' => User::where('role', 'user')->where('status', 'active')->count(),
            'new_today' => User::where('role', 'user')->whereDate('created_at', today())->count(),
            'blocked' => User::where('role', 'user')->where('status', 'blocked')->count(),
        ];
        return view('admin.users', compact('users', 'stats'));
    }

    /**
     * Memperbarui status pengguna (Blokir/Aktifkan).
     */
    public function updateUserStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:active,blocked',
        ]);
        $user = User::findOrFail($id);
        $user->update([
            'status' => $request->status
        ]);
        $message = $request->status == 'blocked' ? 'Pengguna berhasil diblokir.' : 'Pengguna berhasil diaktifkan.';
        return redirect()->route('admin.users')->with('success', $message);
    }

    /**
     * Menampilkan halaman manajemen pembayaran.
     */
    public function payments(Request $request)
    {
        // Ambil input filter
        $startDate = $request->input('tanggal_mulai');
        $endDate = $request->input('tanggal_akhir');
        $status = $request->input('status_pembayaran');

        // Query dasar
        $paymentQuery = Payment::query()->with(['rental.user', 'rental.bicycle', 'rental.package']);
        $statsQuery = Payment::query();
        $rentalStatsQuery = Rental::query();

        // Terapkan filter
        if ($startDate) {
            $paymentQuery->whereDate('payments.created_at', '>=', $startDate);
            $statsQuery->whereDate('payments.created_at', '>=', $startDate);
            $rentalStatsQuery->whereDate('rentals.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $paymentQuery->whereDate('payments.created_at', '<=', $endDate);
            $statsQuery->whereDate('payments.created_at', '<=', $endDate);
            $rentalStatsQuery->whereDate('rentals.created_at', '<=', $endDate);
        }
        if ($status && $status != 'Semua') {
            $paymentQuery->where('status_bayar', $status);
            $statsQuery->where('status_bayar', $status);
        }

        // Ambil data pembayaran utama (sudah difilter)
        $payments = $paymentQuery->latest()->paginate(10);

        // Hitung statistik (yang sudah difilter)
        $stats = [
            'total_revenue' => (clone $statsQuery)->where('status_bayar', 'lunas')->sum('total'),
            'pending_payments' => (clone $statsQuery)->where('status_bayar', 'belum')->count(),
            'total_fines' => (clone $rentalStatsQuery)->sum('denda'),
            'completed_today' => Rental::whereDate('updated_at', today())->where('status', 'selesai')->count(),
        ];

        // Hitung statistik bulanan (data NYATA)
        $monthly_stats = [
            'revenue' => Payment::where('status_bayar', 'lunas')
                                ->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->sum('total'),
            'fines' => Rental::whereMonth('created_at', now()->month)
                             ->whereYear('created_at', now()->year)
                             ->sum('denda'),
        ];

        // Query untuk data Pie Chart (dari query yang sudah difilter)
        $revenueDistribution = (clone $statsQuery)
            ->where('status_bayar', 'lunas')
            ->join('rentals', 'payments.rental_id', '=', 'rentals.id')
            ->join('packages', 'rentals.package_id', '=', 'packages.id')
            ->groupBy('packages.nama_paket')
            ->select(DB::raw('packages.nama_paket as package_name, sum(payments.total) as total_revenue'))
            ->pluck('total_revenue', 'package_name');

        // Kirim semua data ke view
        return view('admin.payments', compact('payments', 'stats', 'monthly_stats', 'revenueDistribution'));
    }

    /**
     * Menampilkan halaman manajemen transaksi.
     */
    public function transactions()
    {
        $transactions = Rental::with(['user', 'bicycle', 'package', 'payment'])
            ->latest()
            ->paginate(15);

        $status_counts = [
            'pending' => Rental::where('status', 'pending')->count(),
            'berjalan' => Rental::where('status', 'berjalan')->count(),
            'selesai' => Rental::where('status', 'selesai')->count(),
            'batal' => Rental::where('status', 'batal')->count(),
        ];
        return view('admin.transactions', compact('transactions', 'status_counts'));
    }

    /**
     * Memperbarui status transaksi (Pending -> Berjalan -> Selesai).
     */
    public function updateTransactionStatus(Request $request, $id)
    {
        $rental = Rental::with(['bicycle', 'package', 'payment'])->findOrFail($id);
        $statusBaru = $request->input('status');

        if (!in_array($statusBaru, ['pending', 'berjalan', 'selesai', 'batal'])) {
            return back()->with('error', 'Status tidak valid.');
        }

        if ($statusBaru === 'berjalan') {
            $rental->update(['status' => 'berjalan']);
            $rental->bicycle->update(['status' => 'rented']);
            Payment::updateOrCreate(
                ['rental_id' => $rental->id],
                [
                    'user_id' => $rental->user_id,
                    'rental_id' => $rental->id,
                    'status_bayar' => 'lunas', // <- Set LUNAS
                    'metode' => 'cash',
                    'total' => $rental->total_cost,
                    'denda' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        elseif ($statusBaru === 'selesai') {
            $returnTime = now();
            $isLate = $returnTime->gt($rental->end_time);
            $denda = 0;
            $minutesLate = 0;
            if ($isLate) {
                $minutesLate = $returnTime->diffInMinutes($rental->end_time);
                $intervals = ceil($minutesLate / 10);
                $denda = $intervals * 5000;
            }
            $rental->update([
                'status' => 'selesai',
                'return_time' => $returnTime,
                'denda' => $denda,
                'lama_telat' => $minutesLate,
            ]);
            $rental->bicycle->update(['status' => 'available']);
            Payment::updateOrCreate(
                ['rental_id' => $rental->id],
                [
                    'user_id' => $rental->user_id,
                    'rental_id' => $rental->id,
                    'status_bayar' => 'lunas', // <- Set LUNAS
                    'metode' => 'cash',
                    'total' => $rental->total_cost + $denda,
                    'denda' => $denda,
                ]
            );
        }
        // PERBAIKAN BUG: Jika 'pending' atau 'batal', set pembayaran ke 'belum'
        else { // Ini akan menangani 'batal' dan 'pending'

            $rental->update(['status' => $statusBaru]);
            $rental->bicycle->update(['status' => 'available']);

            if ($rental->payment) {
                $rental->payment->update(['status_bayar' => 'belum']);
            }
        }

        return back()->with('success', 'Status transaksi & pembayaran berhasil diperbarui.');
    }
}
