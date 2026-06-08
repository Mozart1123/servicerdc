<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

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
     * Artisan profile view (triggers notification).
     */
    public function artisanProfile(User $artisan): View
    {
        if ($artisan->user_type !== 'artisan') {
            abort(404);
        }

        $services = $artisan->services()->active()->verified()->get();

        // Notify artisan
        if (Auth::id() !== $artisan->id) {
            Notification::create([
                'user_id'      => $artisan->id,
                'type'         => 'service_view',
                'related_type' => 'user',
                'related_id'   => Auth::id(),
                'title'        => 'Nouveau prospect',
                'message'      => Auth::user()->name . ' a consulté votre profil.',
                'action_url'   => route('user.dashboard'),
                'is_read'      => false,
            ]);
        }

        return view('user.artisans.show', compact('artisan', 'services'));
    }

    /**
     * Show form to create a new service.
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

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'location'    => ['required', 'string', 'max:255'],
            'images.*'    => ['nullable', 'image', 'max:2048'],
        ]);

        $user = Auth::user();
        $gallery = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $gallery[] = $file->store('service-gallery', 'public');
            }
        }

        $service = Service::create([
            'artisan_id'     => $user->id,
            'category_id'    => $validated['category_id'] ?? null,
            'provider_name'  => $user->name,
            'profession'     => $user->profession ?? 'Artisan',
            'city'           => $validated['location'],
            'phone_number'   => $user->phone ?? '—',
            'title'          => $validated['title'],
            'description'    => $validated['description'] ?? null,
            'price'          => $validated['price'],
            'location'       => $validated['location'],
            'service_image'  => $gallery[0] ?? null,
            'gallery_images' => $gallery,
            'images'         => $gallery,
            'status'         => 'active',
            'is_verified'    => true,
        ]);

        // Notify Admins
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id'      => $admin->id,
                'type'         => 'service_created',
                'related_type' => 'service',
                'related_id'   => $service->id,
                'title'        => 'Nouveau service publié',
                'message'      => "{$user->name} a publié : {$service->title}",
                'action_url'   => route('admin.services.index'),
                'is_read'      => false,
            ]);
        }

        return redirect()->route('user.services.my')->with('success', 'Votre service a été publié avec succès.');
    }

    /**
     * Show form to edit a service.
     */
    public function edit(int $id): View
    {
        $service = Service::findOrFail($id);
        if (Auth::id() !== $service->artisan_id) { abort(403); }
        
        $categories = Category::all();
        return view('user.services.edit', compact('service', 'categories'));
    }

    /**
     * Update a service.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $service = Service::findOrFail($id);
        if (Auth::id() !== $service->artisan_id) { abort(403); }

        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'category_id'   => ['nullable', 'exists:categories,id'],
            'description'   => ['nullable', 'string'],
            'price'         => ['required', 'numeric', 'min:0'],
            'location'      => ['required', 'string', 'max:255'],
            'status'        => ['required', 'in:active,inactive'],
            'service_image' => ['nullable', 'image', 'max:2048'],
            'images.*'      => ['nullable', 'image', 'max:2048'],
        ]);

        $service->update(array_diff_key($validated, ['images' => 1, 'service_image' => 1]));

        // Handle Primary Image Update
        if ($request->hasFile('service_image')) {
            if ($service->service_image) {
                Storage::disk('public')->delete($service->service_image);
            }
            $service->service_image = $request->file('service_image')->store('service-gallery', 'public');
            $service->save();
        }

        // Handle gallery updates
        if ($request->hasFile('images')) {
            $gallery = $service->gallery_images ?? $service->images ?? [];
            if (!is_array($gallery)) { $gallery = []; }

            foreach ($request->file('images') as $file) {
                $gallery[] = $file->store('service-gallery', 'public');
            }

            $service->update(['gallery_images' => $gallery, 'images' => $gallery]);
        }

        return redirect()->route('user.services.my')->with('success', 'Service mis à jour avec succès.');
    }

    /**
     * Remove a single image from the service gallery.
     */
    public function removeImage(Request $request, int $id): RedirectResponse
    {
        $service = Service::findOrFail($id);
        if (Auth::id() !== $service->artisan_id) { abort(403); }

        $index = $request->input('image_index');
        $gallery = $service->gallery_images ?? $service->images ?? [];

        if (is_array($gallery) && isset($gallery[$index])) {
            $path = $gallery[$index];
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            unset($gallery[$index]);
            $newGallery = array_values($gallery);
            
            $service->update([
                'gallery_images' => $newGallery,
                'images' => $newGallery
            ]);

            return back()->with('success', 'Image supprimée.');
        }

        return back()->with('error', 'Image introuvable.');
    }

    /**
     * Delete a service.
     */
    public function destroy(int $id): RedirectResponse
    {
        $service = Service::findOrFail($id);
        if (Auth::id() !== $service->artisan_id) { abort(403); }

        if ($service->service_image) {
            Storage::disk('public')->delete($service->service_image);
        }
        if ($service->gallery_images) {
            foreach ($service->gallery_images as $img) {
                Storage::disk('public')->delete($img);
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
        $user = Auth::user();
        $services = $user->services()->with('category')->latest()->paginate(10);

        $stats = [
            'total'    => $user->services()->count(),
            'verified' => $user->services()->where('is_verified', true)->count(),
            'active'   => $user->services()->where('status', 'active')->count(),
        ];

        return view('user.services.my-services', compact('services', 'stats'));
    }
}
