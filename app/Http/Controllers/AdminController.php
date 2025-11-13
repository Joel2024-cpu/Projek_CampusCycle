<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Bicycle;
use App\Models\Package;
use App\Models\Rental;
use App\Models\Payment;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_bicycles' => Bicycle::count(),
            'available_bicycles' => Bicycle::where('status', 'available')->count(),
            'rented_bicycles' => Bicycle::where('status', 'rented')->count(),
            'total_users' => User::where('role', 'user')->count(),
            'total_rentals' => Rental::count(),
            'total_revenue' => Payment::where('status_bayar', 'lunas')->sum('total'),
            'total_fines' => Rental::sum('denda'),
        ];

        $recent_transactions = Rental::with(['user', 'bicycle'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_transactions'));
    }

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

    public function payments()
    {
        $payments = Payment::with(['rental.user', 'rental.bicycle', 'rental.package'])
            ->latest()
            ->paginate(10);

        $today = now()->toDateString();

        $stats = [
            'total_revenue' => Payment::where('status_bayar', 'lunas')->sum('total'),
            'pending_payments' => Payment::where('status_bayar', 'belum')->count(),
            'total_fines' => Rental::sum('denda'),
            'completed_today' => Rental::whereDate('updated_at', $today)
                                ->where('status', 'selesai')
                                ->count(),
        ];
        return view('admin.payments', compact('payments', 'stats'));
    }

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
                    'status_bayar' => 'lunas',
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
                    'status_bayar' => 'lunas',
                    'metode' => 'cash',
                    'total' => $rental->total_cost + $denda,
                    'denda' => $denda,
                ]
            );
        }
        elseif ($statusBaru === 'batal') {
            $rental->update(['status' => 'batal']);
            $rental->bicycle->update(['status' => 'available']);

            if ($rental->payment) {
                $rental->payment->update(['status_bayar' => 'belum']);
            }
        }
        else {
            $rental->update(['status' => 'pending']);
        }
        return back()->with('success', 'Status transaksi & pembayaran berhasil diperbarui.');
    }

    public function reports()
    {
        $month = now()->month;
        $year = now()->year;

        $monthly_rentals = Rental::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->count();

        $monthly_revenue = Rental::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'selesai')
            ->sum('total_cost');

        $popular_bicycles = Bicycle::withCount(['rentals' => function ($query) {
            $query->where('status', 'selesai');
        }])
            ->orderBy('rentals_count', 'desc')
            ->take(5)
            ->get();

        $reports = [
            'monthly_rentals' => $monthly_rentals,
            'monthly_revenue' => $monthly_revenue,
            'popular_bicycles' => $popular_bicycles,
            'active_users' => User::where('role', 'user')
                ->whereHas('rentals', function ($query) use ($month) {
                    $query->whereMonth('created_at', $month);
                })
                ->count(),
        ];

        return view('admin.reports', compact('reports'));
    }
}
