<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::latest()->paginate(10);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('admin.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'is_verified' => 'boolean',
        ]);

        $validated['artisan_id'] = auth()->id();
        $validated['status'] = 'active';

        Service::create($validated);

        return redirect()->route('admin.moderation.services')->with('success', 'Service créé avec succès.');
    }

    public function edit(Service $service)
    {
        $categories = \App\Models\Category::all();
        return view('admin.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'is_verified' => 'boolean',
        ]);

        $service->update($validated);

        return redirect()->route('admin.moderation.services')->with('success', 'Service mis à jour avec succès.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.moderation.services')->with('success', 'Service supprimé avec succès.');
    }
}
