<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bicycle;
use Illuminate\Support\Facades\Storage; 

class BicycleController extends Controller
{
    public function index()
    {
        $bicycles = Bicycle::withCount(['rentals' => function ($query) {
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


    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_sepeda' => 'required|string|max:50|unique:bicycles,kode_sepeda',
            'merk' => 'required|string|max:100',
            'type' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|string|in:available,rented,maintenance',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('bicycles', 'public');
        }

        Bicycle::create($data);

        return redirect()->route('admin.bicycles')->with('success', 'Sepeda berhasil ditambahkan!');
    }


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

        if ($request->hasFile('image')) {
            if ($bicycle->image) {
                Storage::disk('public')->delete($bicycle->image);
            }
            $data['image'] = $request->file('image')->store('bicycles', 'public');
        } else {
             $data = $request->except('image');
        }

        $bicycle->update($data);
        
        return redirect()->route('admin.bicycles')->with('success', 'Sepeda berhasil diperbarui!');
    }

    
    public function destroy($id)
    {
        $bicycle = Bicycle::findOrFail($id);

        if ($bicycle->status == 'rented') {
             return back()->with('error', 'Tidak dapat menghapus sepeda yang sedang disewa.');
        }

        if ($bicycle->image) {
            Storage::disk('public')->delete($bicycle->image);
        }

        $bicycle->delete();
        
        return redirect()->route('admin.bicycles')->with('success', 'Sepeda berhasil dihapus!');
    }
}
