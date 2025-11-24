<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::withCount('rentals')->get(); // Menghitung berapa kali paket dipakai
        return view('admin.packages', compact('packages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:100',
            'durasi_jam' => 'required|integer|min:1',
            'harga' => 'required|integer|min:0',
        ]);

        Package::create($request->all());
        return redirect()->route('admin.packages')->with('success', 'Paket berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $package = Package::findOrFail($id);

        $request->validate([
            'nama_paket' => 'required|string|max:100',
            'durasi_jam' => 'required|integer|min:1',
            'harga' => 'required|integer|min:0',
        ]);

        $package->update($request->all());
        return redirect()->route('admin.packages')->with('success', 'Paket berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $package = Package::findOrFail($id);

        // Cek jika paket sedang dipakai di transaksi aktif
        if ($package->rentals()->where('status', 'berjalan')->exists()) {
            return back()->with('error', 'Paket sedang digunakan dalam transaksi aktif, tidak bisa dihapus.');
        }

        $package->delete();
        return redirect()->route('admin.packages')->with('success', 'Paket berhasil dihapus!');
    }
}
