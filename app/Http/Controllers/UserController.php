<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bicycle;
use App\Models\Rental;
use App\Models\Package;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $activeRentals = Rental::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'berjalan'])
            ->count();
        $totalRentals = Rental::where('user_id', $user->id)->count();

        // Ambil SEMUA rental untuk proses auto-check dan sync denda
        $allRentals = Rental::where('user_id', $user->id)
            ->with(['bicycle', 'package'])
            ->get();

        // Auto check untuk SEMUA overdue rentals & sync denda (TANPA ubah status)
        foreach ($allRentals as $rental) {
            $this->syncRentalStatusAndDenda($rental);
        }

        // Ambil 5 data terbaru untuk ditampilkan (setelah sync)
        $rentals = Rental::where('user_id', $user->id)
            ->with(['bicycle', 'package'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($rental) {
                // Pastikan denda ter-update
                $rental->syncDenda();
                return $rental;
            });

        // Hitung total denda yang sudah di-sync
        $totalDenda = $rentals->sum('denda');

        return view('user.dashboard', compact('user', 'rentals', 'activeRentals', 'totalRentals', 'totalDenda'));
    }

    public function bicycles()
    {
        $bicycles = Bicycle::select(
                            'merk',
                            'type',
                            'description',
                            'image',
                            DB::raw('MIN(id) as id'),
                            DB::raw('COUNT(id) as available_stock')
                        )
                        ->where('status', 'available')
                        ->groupBy('merk', 'type', 'description', 'image')
                        ->get();

        return view('user.bicycles', compact('bicycles'));
    }

    public function showRentForm($id)
    {
        $bicycle = Bicycle::findOrFail($id);
        $packages = Package::all();

        return view('user.form', compact('bicycle', 'packages'));
    }

    public function rentBicycle(Request $request, $id)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'start_time' => 'required|date|after_or_equal:now'
        ], [
            'package_id.required' => 'Silakan pilih paket sewa.',
            'start_time.after_or_equal' => 'Waktu mulai harus sekarang atau setelahnya.'
        ]);

        $user = Auth::user();

        $clickedBicycle = Bicycle::findOrFail($id);

        $bicycleToRent = Bicycle::where('merk', $clickedBicycle->merk)
                                ->where('type', $clickedBicycle->type)
                                ->where('status', 'available')
                                ->first();

        if (!$bicycleToRent) {
            return back()->with('error', 'Maaf, stok sepeda jenis ini baru saja habis.');
        }

        $package = Package::findOrFail($request->package_id);
        $start = Carbon::parse($request->start_time);
        $end = $start->copy()->addHours($package->durasi_jam);

        Rental::create([
            'user_id' => $user->id,
            'bicycle_id' => $bicycleToRent->id,
            'package_id' => $package->id,
            'start_time' => $start,
            'end_time' => $end,
            'total_cost' => $package->harga,
            'denda' => 0,
            'status' => 'pending',
        ]);

        $bicycleToRent->update(['status' => 'rented']);

        return redirect()->route('user.dashboard')->with('success', 'Berhasil menyewa sepeda! Menunggu konfirmasi admin.');
    }

    public function history()
    {
        // Ambil SEMUA rental untuk proses auto-check dan sync denda
        $allRentals = Rental::where('user_id', Auth::id())
            ->with(['bicycle', 'package'])
            ->get();

        // Auto check untuk SEMUA overdue rentals & sync denda
        foreach ($allRentals as $rental) {
            $this->syncRentalStatusAndDenda($rental);
        }

        // Ambil semua data untuk ditampilkan di history (setelah sync)
        $rentals = Rental::where('user_id', Auth::id())
            ->with(['bicycle', 'package'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($rental) {
                // Pastikan denda ter-update
                $rental->syncDenda();
                return $rental;
            });

        return view('user.history', compact('rentals'));
    }

    public function returnBicycle($id)
    {
        $rental = Rental::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('bicycle')
            ->firstOrFail();

        if ($rental->status !== 'berjalan') {
            return back()->with('error', 'Sepeda ini tidak sedang disewa.');
        }

        $returnTime = Carbon::now();
        $endTime = Carbon::parse($rental->end_time);
        $isLate = $returnTime->gt($endTime);

        // Hitung denda
        $denda = 0;
        if ($isLate) {
            $minutesLate = $returnTime->diffInMinutes($endTime);
            $blocksOfTenMinutes = ceil($minutesLate / 10);
            $denda = $blocksOfTenMinutes * 5000;
        }

        // DEBUG: Log data sebelum update
        \Log::info('Pengembalian Sepeda:', [
            'rental_id' => $rental->id,
            'user_id' => Auth::id(),
            'end_time' => $rental->end_time,
            'return_time' => $returnTime,
            'is_late' => $isLate,
            'minutes_late' => $isLate ? $minutesLate : 0,
            'denda_calculated' => $denda,
            'before_status' => $rental->status
        ]);

        // UPDATE langsung ke database (PASTI jalan)
        $rental->update([
            'return_time' => $returnTime,
            'status' => 'selesai', // PASTIKAN status berubah
            'denda' => $denda,
        ]);

        // Update status sepeda
        $rental->bicycle->update(['status' => 'available']);

        // DEBUG: Log setelah update
        $updatedRental = Rental::find($rental->id);
        \Log::info('Setelah Update:', [
            'rental_id' => $updatedRental->id,
            'after_status' => $updatedRental->status,
            'after_return_time' => $updatedRental->return_time,
            'after_denda' => $updatedRental->denda
        ]);

        return back()->with('success', 'Sepeda berhasil dikembalikan! ' .
            ($denda > 0 ? "Denda: Rp " . number_format($denda, 0, ',', '.') : ''));
    }

    // METHOD BARU: Helper untuk sync status dan denda (TANPA ubah status)
    private function syncRentalStatusAndDenda($rental)
    {
        // Hanya sync denda, TIDAK ubah status
        // Untuk rental berjalan yang terlambat, biarkan status tetap 'berjalan'
        // tapi hitung denda-nya
        $rental->syncDenda();
    }
}
