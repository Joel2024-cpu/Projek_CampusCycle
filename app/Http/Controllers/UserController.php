<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bicycle;
use App\Models\Rental;
use App\Models\Package;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $activeRentals = Rental::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'berjalan'])
            ->count();
        $totalRentals = Rental::where('user_id', $user->id)->count();
        $rentals = Rental::where('user_id', $user->id)
            ->with(['bicycle', 'package'])
            ->latest()
            ->take(5)
            ->get();
        foreach ($rentals as $rental) {
            if ($rental->status === 'berjalan' && now()->gt($rental->end_time)) {
                $rental->update(['status' => 'selesai']);
                $rental->bicycle->update(['status' => 'available']);
            }
        }

        return view('user.dashboard', compact('user', 'rentals', 'activeRentals', 'totalRentals'));
    }

    public function bicycles()
    {
        $bicycles = Bicycle::where('status', 'available')->get();
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
        $bicycle = Bicycle::findOrFail($id);
        $package = Package::findOrFail($request->package_id);

        if ($bicycle->status !== 'available') {
            return back()->with('error', 'Sepeda tidak tersedia.');
        }

        $start = Carbon::parse($request->start_time);
        $end = $start->copy()->addHours($package->durasi_jam);

        Rental::create([
            'user_id' => $user->id,
            'bicycle_id' => $bicycle->id,
            'package_id' => $package->id,
            'start_time' => $start,
            'end_time' => $end,
            'total_cost' => $package->harga,
            'denda' => 0,
            'status' => 'pending',
        ]);

        $bicycle->update(['status' => 'rented']);

        return redirect()->route('user.dashboard')->with('success', 'Berhasil menyewa sepeda! Menunggu konfirmasi admin.');
    }

    public function history()
    {
        $rentals = Rental::where('user_id', Auth::id())
            ->with(['bicycle', 'package'])
            ->orderBy('created_at', 'desc')
            ->get();
        foreach ($rentals as $rental) {
            if ($rental->status === 'selesai' && $rental->bicycle->status !== 'available') {
                $rental->bicycle->update(['status' => 'available']);
            }
        }

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
        $isLate = $returnTime->gt(Carbon::parse($rental->end_time));

        $denda = 0;
        if ($isLate) {
            $minutesLate = $returnTime->diffInMinutes(Carbon::parse($rental->end_time));
            $blocksOfTenMinutes = ceil($minutesLate / 10);
            $denda = $blocksOfTenMinutes * 5000;
        }

        $rental->update([
            'return_time' => $returnTime,
            'status' => $isLate ? 'terlambat' : 'selesai',
            'denda' => $denda,
        ]);

        $rental->bicycle->update(['status' => 'available']);

        return back()->with('success', 'Sepeda berhasil dikembalikan! ' . ($isLate ? "Denda: Rp" . number_format($denda, 0, ',', '.') : ''));
    }
}
