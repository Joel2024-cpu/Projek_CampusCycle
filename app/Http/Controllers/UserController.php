<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bicycle;
use App\Models\Rental;
use App\Models\Package;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

        $this->syncAllRentalDenda($user->id);

        $rentals = Rental::where('user_id', $user->id)
            ->with(['bicycle', 'package'])
            ->latest()
            ->take(5)
            ->get();

        $totalDenda = $rentals->sum(function($rental) {
            return $rental->denda + $rental->denda_kerusakan;
        });

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
        $this->syncAllRentalDenda(Auth::id());

        $rentals = Rental::where('user_id', Auth::id())
            ->with(['bicycle', 'package'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.history', compact('rentals'));
    }


    public function returnBicycle($id)
    {
        $rental = Rental::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['bicycle', 'package'])
            ->firstOrFail();

        if ($rental->status !== 'berjalan') {
            return back()->with('error', 'Sepeda ini tidak sedang disewa.');
        }

        $returnTime = Carbon::now('Asia/Jakarta');

        $isLate = $returnTime->gt($rental->end_time);
        $dendaKeterlambatan = 0;

        if ($isLate) {
            $minutesLate = abs($returnTime->diffInMinutes($rental->end_time));
            $intervals = ceil($minutesLate / 10);
            $dendaKeterlambatan = $intervals * 5000;
        }

        $dendaKerusakan = 0;
        $biayaPaket = $rental->package->harga ?? 2500;
        $totalBiaya = $biayaPaket + $dendaKeterlambatan + $dendaKerusakan;

        $rental->update([
            'status' => 'selesai',
            'return_time' => $returnTime,
            'denda' => $dendaKeterlambatan,
            'total_cost' => $totalBiaya,
        ]);

        $rental->bicycle->update(['status' => 'available']);

        return back()->with('success', 'Sepeda dikembalikan! Total Bayar: Rp ' . number_format($totalBiaya, 0, ',', '.'));
    }


    private function syncAllRentalDenda($userId)
    {
        $rentals = Rental::where('user_id', $userId)->get();

        foreach ($rentals as $rental) {
            $this->calculateAndUpdateDendaKeterlambatan($rental);
        }
    }

    private function calculateAndUpdateDendaKeterlambatan($rental)
    {
        if ($rental->status === 'berjalan' && !$rental->return_time) {
            $endTime = Carbon::parse($rental->end_time);
            $now = Carbon::now();

            if ($now->gt($endTime)) {
                $minutesLate = $now->diffInMinutes($endTime);
                $blocksOfTenMinutes = ceil($minutesLate / 10);
                $dendaKeterlambatan = $blocksOfTenMinutes * 5000;

                $rental->update(['denda' => $dendaKeterlambatan]);
            }
        }
    }


    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
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
            'name.required' => 'Nama wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan pengguna lain.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
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
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
