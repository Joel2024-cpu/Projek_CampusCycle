<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Bicycle;
use App\Models\Package;
use App\Models\Rental;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        $rentals = Rental::with(['user', 'bicycle'])
            ->latest()
            ->take(5)
            ->get();

        $today_stats = [
            'new_rentals' => Rental::whereDate('created_at', today())->count(),
            'new_users' => User::where('role', 'user')->whereDate('created_at', today())->count(),
            'revenue_today' => Payment::where('status_bayar', 'lunas')->whereDate('updated_at', today())->sum('total'),
            'pending_transactions' => Rental::where('status', 'pending')->count(),
        ];

        $totalRentals = $stats['total_rentals'];

        return view('admin.dashboard', compact('stats', 'rentals', 'today_stats', 'totalRentals'));
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


    public function payments(Request $request)
    {
        $startDate = $request->input('tanggal_mulai');
        $endDate = $request->input('tanggal_akhir');
        $status = $request->input('status_pembayaran');

        $paymentQuery = Payment::query()->with(['rental.user', 'rental.bicycle', 'rental.package']);
        $statsQuery = Payment::query();
        $rentalStatsQuery = Rental::query();

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

        $payments = $paymentQuery->latest()->paginate(10);

        $stats = [
            'total_revenue' => (clone $statsQuery)->where('status_bayar', 'lunas')->sum('total'),
            'pending_payments' => (clone $statsQuery)->where('status_bayar', 'belum')->count(),
            'total_fines' => (clone $rentalStatsQuery)->sum('denda'),
            'completed_today' => Rental::whereDate('updated_at', today())->where('status', 'selesai')->count(),
        ];

        $monthly_stats = [
            'revenue' => Payment::where('status_bayar', 'lunas')
                                ->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->sum('total'),
            'fines' => Rental::whereMonth('created_at', now()->month)
                             ->whereYear('created_at', now()->year)
                             ->sum('denda'),
        ];

        $revenueDistribution = (clone $statsQuery)
            ->where('status_bayar', 'lunas')
            ->join('rentals', 'payments.rental_id', '=', 'rentals.id')
            ->join('packages', 'rentals.package_id', '=', 'packages.id')
            ->groupBy('packages.nama_paket')
            ->select(DB::raw('packages.nama_paket as package_name, sum(payments.total) as total_revenue'))
            ->pluck('total_revenue', 'package_name');
        return view('admin.payments', compact('payments', 'stats', 'monthly_stats', 'revenueDistribution'));
    }


    public function transactions(Request $request)
    {
        $search = $request->input('search');

        $query = Rental::with(['user', 'bicycle', 'package', 'payment'])->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('bicycle', function($b) use ($search) {
                      $b->where('kode_sepeda', 'like', "%{$search}%")
                        ->orWhere('merk', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->paginate(15)->appends(['search' => $search]);
        
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
        try {
            \Log::info('Admin updating transaction status', ['id' => $id, 'status' => $request->status]);

            $rental = Rental::with(['bicycle', 'package', 'payment'])->findOrFail($id);
            
            $statusBaru = $request->input('status');

            if (!in_array($statusBaru, ['pending', 'berjalan', 'selesai', 'batal'])) {
                return back()->with('error', 'Status tidak valid.');
            }
            
            $returnTime = Carbon::now('Asia/Jakarta');
            
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
                        'created_at' => $returnTime,
                        'updated_at' => $returnTime,
                    ]
                );

                return back()->with('success', 'Sewa berhasil dimulai!');
            }

            elseif ($statusBaru === 'selesai') {
                $isLate = $returnTime->gt($rental->end_time);
                $dendaKeterlambatan = 0;
                if ($isLate) {
                    $minutesLate = abs($returnTime->diffInMinutes($rental->end_time));
                    $intervals = ceil($minutesLate / 10);
                    $dendaKeterlambatan = $intervals * 5000;
                }

                $dendaKerusakan = (int) $request->input('denda_kerusakan', 0);

                $biayaPaket = $rental->package->harga ?? 0;
                if ($biayaPaket <= 0) {
                    $biayaPaket = 2500; 
                }

                $totalBiaya = $biayaPaket + $dendaKeterlambatan + $dendaKerusakan;
                $rental->update([
                    'status' => 'selesai',
                    'return_time' => $returnTime,
                    'denda' => $dendaKeterlambatan, 
                    'total_cost' => $totalBiaya,
                ]);

                $rental->bicycle->update(['status' => 'available']);

                Payment::updateOrCreate(
                    ['rental_id' => $rental->id],
                    [
                        'user_id' => $rental->user_id,
                        'rental_id' => $rental->id,
                        'status_bayar' => 'lunas',
                        'metode' => 'cash',
                        'total' => $totalBiaya, 
                        'denda' => $dendaKeterlambatan + $dendaKerusakan, 
                    ]
                );

                \Log::info('Transaction completed', [
                    'rental_id' => $rental->id,
                    'biaya_paket' => $biayaPaket,
                    'denda_keterlambatan' => $dendaKeterlambatan,
                    'denda_kerusakan' => $dendaKerusakan,
                    'total_biaya' => $totalBiaya
                ]);

                return back()->with('success', 'Sewa selesai! Total Bayar: Rp ' . number_format($totalBiaya, 0, ',', '.'));
            }

            else {
                $rental->update(['status' => $statusBaru]);
                $rental->bicycle->update(['status' => 'available']);
                if ($rental->payment) {
                    $rental->payment->update(['status_bayar' => 'belum']);
                }

                return back()->with('success', 'Status transaksi diperbarui.');
            }

        } catch (\Exception $e) {
            \Log::error('Error in updateTransactionStatus: ' . $e->getMessage());
            return back()->with('error', 'Terjadi error sistem. Cek log untuk detail.');
        }
    }


    public function profile()
    {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|min:6|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'email.unique' => 'Email ini sudah digunakan.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'profile_picture.image' => 'File harus berupa gambar.',
            'profile_picture.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return back()->with('success', 'Profil Admin berhasil diperbarui!');
    }
}
