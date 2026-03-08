<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * User Service Controller
 * 
 * Handles service-related functionality for artisans and clients.
 */
class ServiceController extends Controller
{
    /**
     * Display all services with filters.
     */
    public function index(Request $request): View
    {
        $query = Service::active()->verified();

        if ($request->has('category') && $request->category) {
            $query->byCategory($request->category);
        }

        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        if ($request->has('location') && $request->location) {
            $query->byLocation($request->location);
        }

        $services = $query->with('category', 'artisan')
                         ->latest()
                         ->paginate(12)
                         ->appends($request->query());
        
        $categories = Category::all();

        return view('user.services.index', compact('services', 'categories'));
    }

    /**
     * Display service details.
     */
    public function show(int $id): View
    {
        $service = Service::with('category', 'artisan')
                         ->findOrFail($id);
        
        $relatedServices = Service::active()
                                 ->verified()
                                 ->where('category_id', $service->category_id)
                                 ->where('id', '!=', $id)
                                 ->take(4)
                                 ->get();

        return view('user.services.show', compact('service', 'relatedServices'));
    }

    /**
     * Show form to create a new service (artisans only).
     */
    public function create(): View
    {
        if (!Auth::user()->isArtisan()) {
            abort(403, 'Seuls les artisans peuvent créer des services.');
        }
        
        $categories = Category::all();
        return view('user.services.create', compact('categories'));
    }

    /**
     * Store a new service.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!Auth::user()->isArtisan()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string', 'min:50'],
            'price' => ['required', 'numeric', 'min:0'],
            'location' => ['required', 'string', 'max:255'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('services', 'public');
                $images[] = $path;
            }
        }

        Service::create([
            'artisan_id' => Auth::id(),
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'location' => $request->location,
            'images' => $images,
            'status' => 'active',
            'is_verified' => false,
        ]);

        return redirect()->route('user.services.my')->with('success', 'Service créé avec succès. En attente de vérification.');
    }

    /**
     * Show form to edit a service.
     */
    public function edit(int $id): View
    {
        $service = Service::findOrFail($id);
        
        if (Auth::id() !== $service->artisan_id) {
            abort(403, 'Unauthorized');
        }
        
        $categories = Category::all();
        return view('user.services.edit', compact('service', 'categories'));
    }

    /**
     * Update a service.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $service = Service::findOrFail($id);
        
        if (Auth::id() !== $service->artisan_id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string', 'min:50'],
            'price' => ['required', 'numeric', 'min:0'],
            'location' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $images = $service->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('services', 'public');
                $images[] = $path;
            }
        }

        $service->update([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'location' => $request->location,
            'images' => array_unique($images),
            'status' => $request->status,
        ]);

        return redirect()->route('user.services.my')->with('success', 'Service mis à jour.');
    }

    /**
     * Delete a service.
     */
    public function destroy(int $id): RedirectResponse
    {
        $service = Service::findOrFail($id);
        
        if (Auth::id() !== $service->artisan_id) {
            abort(403, 'Unauthorized');
        }

        // Delete images
        if ($service->images) {
            foreach ($service->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $service->delete();
        return redirect()->route('user.services.my')->with('success', 'Service supprimé.');
    }

    /**
     * Display user's services.
     */
    public function myServices(): View
    {
        if (!Auth::user()->isArtisan()) {
            abort(403, 'Seuls les artisans peuvent voir leurs services.');
        }
        
        $services = Auth::user()->services()
                               ->with('category')
                               ->latest()
                               ->paginate(10);

        $stats = [
            'total' => Auth::user()->services()->count(),
            'verified' => Auth::user()->services()->where('is_verified', true)->count(),
            'active' => Auth::user()->services()->where('status', 'active')->count(),
        ];

        return view('user.services.my-services', compact('services', 'stats'));
    }

    /**
     * Remove image from service.
     */
    public function removeImage(Request $request, int $id): RedirectResponse
    {
        $service = Service::findOrFail($id);
        
        if (Auth::id() !== $service->artisan_id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'image_path' => ['required', 'string'],
        ]);

        $images = $service->images ?? [];
        $key = array_search($request->image_path, $images);
        
        if ($key !== false) {
            Storage::disk('public')->delete($images[$key]);
            unset($images[$key]);
            $service->update(['images' => array_values($images)]);
        }

        return redirect()->back()->with('success', 'Image supprimée.');
    }
}
