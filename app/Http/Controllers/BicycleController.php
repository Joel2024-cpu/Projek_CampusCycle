<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bicycle;

class BicycleController extends Controller
{
    public function index()
    {
        $bicycles = Bicycle::withCount('rentals')->paginate(10);

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
            'status' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
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
            'status' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('bicycles', 'public');
        }

        $bicycle->update($data);
        return redirect()->route('admin.bicycles')->with('success', 'Sepeda berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $bicycle = Bicycle::findOrFail($id);
        if ($bicycle->image && file_exists(storage_path('app/public/' . $bicycle->image))) {
            unlink(storage_path('app/public/' . $bicycle->image));
        }
        $bicycle->delete();

        return redirect()->route('admin.bicycles')->with('success', 'Sepeda berhasil dihapus!');
    }
}
