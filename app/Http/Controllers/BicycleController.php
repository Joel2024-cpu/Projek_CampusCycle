<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bicycle;
use Illuminate\Support\Facades\Storage; // WAJIB: Import ini untuk menghapus file

class BicycleController extends Controller
{
    // READ: Menampilkan halaman data sepeda
    public function index()
    {
        $bicycles = Bicycle::withCount(['rentals' => function ($query) {
            // Hitung hanya rental yang sudah selesai
            $query->where('status', 'selesai');
        }])
        ->latest()->paginate(10);

        $stats = [
            'total' => Bicycle::count(),
            'available' => Bicycle::where('status', 'available')->count(),
            'rented' => Bicycle::where('status', 'rented')->count(),
            'maintenance' => Bicycle::where('status', 'maintenance')->count(),
        ];

        return view('admin.bicycles', compact('bicycles', 'stats'));
    }

    // --- CREATE: LOGIKA TAMBAH SEPEDA (FIX VALIDASI STATUS) ---
    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_sepeda' => 'required|string|max:50|unique:bicycles,kode_sepeda',
            'merk' => 'required|string|max:100',
            'type' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            // FIX: Tambah validasi 'in' untuk status
            'status' => 'required|string|in:available,rented,maintenance',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('bicycles', 'public');
        }

        Bicycle::create($data);
        return redirect()->route('admin.bicycles')->with('success', 'Sepeda berhasil ditambahkan!');
    }

    // --- UPDATE: LOGIKA EDIT SEPEDA (IMAGE HANDLING AMAN) ---
    public function update(Request $request, $id)
    {
        $bicycle = Bicycle::findOrFail($id);
        $data = $request->validate([
            'kode_sepeda' => 'required|string|max:50|unique:bicycles,kode_sepeda,' . $bicycle->id,
            'merk' => 'required|string|max:100',
            'type' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|string|in:available,rented,maintenance', // FIX VALIDASI
            'image' => 'nullable|image|max:2048'
        ]);

        // 1. Image Handling
        if ($request->hasFile('image')) {
            // Hapus gambar lama sebelum menyimpan yang baru
            if ($bicycle->image) {
                Storage::disk('public')->delete($bicycle->image);
            }
            $data['image'] = $request->file('image')->store('bicycles', 'public');
        } else {
             // Jika tidak ada upload baru, gunakan semua data kecuali 'image' agar path lama tidak hilang
             $data = $request->except('image');
        }

        $bicycle->update($data);
        return redirect()->route('admin.bicycles')->with('success', 'Sepeda berhasil diperbarui!');
    }

    // --- DELETE: LOGIKA HAPUS SEPEDA (SAFETY CHECK & STORAGE DELETE) ---
    public function destroy($id)
    {
        $bicycle = Bicycle::findOrFail($id);

        // Safety Check: Tolak jika sepeda sedang disewa
        if ($bicycle->status == 'rented') {
             return back()->with('error', 'Tidak dapat menghapus sepeda yang sedang disewa.');
        }

        // Hapus Gambar dari Storage dengan cara yang benar
        if ($bicycle->image) {
            Storage::disk('public')->delete($bicycle->image);
        }

        $bicycle->delete();
        return redirect()->route('admin.bicycles')->with('success', 'Sepeda berhasil dihapus!');
    }
}
