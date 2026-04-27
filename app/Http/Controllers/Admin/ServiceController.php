<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('sort_order')->get();
        return view('admin.services', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:50',
            'type'        => 'required|in:kiloan,satuan',
            'price'       => 'required|numeric|min:0',
            'unit'        => 'required|string|max:20',
            'icon'        => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer|min:0',
        ]);
        Service::create($validated);
        return back()->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:50',
            'type'        => 'required|in:kiloan,satuan',
            'price'       => 'required|numeric|min:0',
            'unit'        => 'required|string|max:20',
            'icon'        => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
            'sort_order'  => 'nullable|integer|min:0',
        ]);
        $service->update($validated);
        return back()->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('success', 'Layanan berhasil dihapus.');
    }
}
