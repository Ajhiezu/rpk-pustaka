<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $locations = Location::latest()
            ->when($search, function($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })
            ->paginate(10);
            
        return view('admin.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
            'description' => 'nullable|string|max:500',
        ]);

        Location::create($validated);

        return redirect()->route('admin.locations.index')->with('success', 'Lokasi Rak berhasil ditambahkan!');
    }

    public function show(Location $location, Request $request)
    {
        $search = $request->input('search');
        
        $books = $location->books()
            ->with(['category', 'location'])
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('author', 'LIKE', "%{$search}%")
                      ->orWhere('isbn', 'LIKE', "%{$search}%")
                      ->orWhere('book_code', 'LIKE', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);
            
        return view('admin.locations.show', compact('location', 'books'));
    }

    public function edit(Location $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,' . $location->id,
            'description' => 'nullable|string|max:500',
        ]);

        $location->update($validated);

        return redirect()->route('admin.locations.index')->with('success', 'Lokasi Rak berhasil diperbarui!');
    }

    public function destroy(Location $location)
    {
        if ($location->books()->count() > 0) {
            return redirect()->back()->with('error', 'Lokasi Rak "'.$location->name.'" tidak dapat dihapus karena masih digunakan oleh '.$location->books()->count().' koleksi buku.');
        }

        $location->delete();

        return redirect()->route('admin.locations.index')->with('success', 'Lokasi Rak berhasil dihapus!');
    }
}
