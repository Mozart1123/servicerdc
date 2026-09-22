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
    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('public.services.index', $request->query());
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
     * Store a new service (supports multiple sub-service types at once).
     *
     * IMPORTANT — chaque type de service sélectionné crée sa PROPRE offre avec
     * son propre titre, son propre prix, sa propre description et ses propres
     * photos (champs "titles[id]", "prices[id]", "descriptions[id]" et
     * "images_by_type[id][]", indexés par service_type_id). Avant, un seul
     * titre et un seul jeu de photos étaient partagés par toutes les offres
     * créées en une fois, ce qui faisait que plusieurs services publiés
     * ensemble se retrouvaient avec un titre et des photos identiques.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!Auth::user()->isArtisan()) {
            abort(403, 'Unauthorized');
        }

        $user = Auth::user();

        $activeSubscription = $user->activeSubscription;
        $maxServices = $activeSubscription && $activeSubscription->subscriptionPlan
            ? $activeSubscription->subscriptionPlan->max_services
            : 1;

        $currentServicesCount = $user->services()->active()->count();
        $slotsRemaining = $maxServices - $currentServicesCount;

        $base = $request->validate([
            'category_id'          => ['required', 'exists:categories,id'],
            'service_type_ids'     => ['nullable', 'array'],
            'service_type_ids.*'   => ['nullable', 'exists:service_types,id'],
            'location'             => ['required', 'string', 'max:255'],
        ]);

        $serviceTypeIds = $base['service_type_ids'] ?? [];
        // Normalize: if none checked, treat as single offer with no type
        $offersToCreate = empty($serviceTypeIds) ? [null] : $serviceTypeIds;
        $offersCount    = count($offersToCreate);

        // Check subscription limit for all planned offers at once
        if ($currentServicesCount + $offersCount > $maxServices) {
            $allowed = max(0, $slotsRemaining);
            return back()
                ->with('error', "Votre abonnement permet {$maxServices} service(s) actif(s). Il vous reste {$allowed} slot(s) disponible(s), mais vous tentez d'en créer {$offersCount}. Réduisez votre sélection ou passez à un abonnement supérieur.")
                ->with('upgrade_url', route('user.subscription.index'))
                ->withInput();
        }

        // Règles de validation propres à chaque offre : un jeu de champs
        // "génériques" quand aucun type n'est coché (une seule offre), ou un
        // jeu de champs par type sélectionné (titles.{id}, prices.{id}, ...).
        $rules = [];
        $durationsList    = implode(',', array_keys(Service::DURATIONS));
        $availabilitiesList = implode(',', array_keys(Service::AVAILABILITIES));
        $minNoticesList   = implode(',', array_keys(Service::MIN_NOTICES));

        if (empty($serviceTypeIds)) {
            $rules['title']            = ['required', 'string', 'max:255'];
            $rules['pricing_type']     = ['required', 'in:fixed,starting_from,quote'];
            $rules['price']            = ['nullable', 'numeric', 'min:0', 'required_unless:pricing_type,quote'];
            $rules['duration']         = ['required', "in:{$durationsList}"];
            $rules['service_location'] = ['required', 'in:home,provider,both'];
            $rules['availability']     = ['required', "in:{$availabilitiesList}"];
            $rules['min_notice']       = ['nullable', 'required_if:availability,appointment', "in:{$minNoticesList}"];
            $rules['description']      = ['nullable', 'string'];
            $rules['images']           = ['nullable', 'array', 'max:5'];
            $rules['images.*']         = ['nullable', 'image', 'max:5120'];
        } else {
            foreach ($serviceTypeIds as $id) {
                $rules["titles.$id"]            = ['required', 'string', 'max:255'];
                $rules["pricing_types.$id"]     = ['required', 'in:fixed,starting_from,quote'];
                $rules["prices.$id"]            = ['nullable', 'numeric', 'min:0', "required_unless:pricing_types.$id,quote"];
                $rules["durations.$id"]         = ['required', "in:{$durationsList}"];
                $rules["service_locations.$id"] = ['required', 'in:home,provider,both'];
                $rules["availabilities.$id"]     = ['required', "in:{$availabilitiesList}"];
                $rules["min_notices.$id"]       = ['nullable', "required_if:availabilities.$id,appointment", "in:{$minNoticesList}"];
                $rules["descriptions.$id"]      = ['nullable', 'string'];
                $rules["images_by_type.$id"]    = ['nullable', 'array', 'max:5'];
                $rules["images_by_type.$id.*"]  = ['nullable', 'image', 'max:5120'];
            }
        }
        $request->validate($rules);

        $createdServices = [];
        foreach ($offersToCreate as $serviceTypeId) {
            if ($serviceTypeId === null) {
                // Une seule offre, sans type précis — champs génériques.
                $serviceTitle       = $request->input('title');
                $pricingType        = (string) $request->input('pricing_type', 'fixed');
                $servicePrice       = $pricingType === 'quote' ? null : $request->input('price');
                $duration           = $request->input('duration');
                $serviceLocation    = $request->input('service_location');
                $availability       = $request->input('availability');
                $minNotice          = $availability === 'appointment' ? $request->input('min_notice') : null;
                $serviceDescription = $request->input('description');
                $files              = $request->file('images', []) ?? [];
            } else {
                // Une offre par type coché — chacune avec ses propres valeurs.
                $st = \App\Models\ServiceType::find($serviceTypeId);
                $typedTitle   = trim((string) $request->input("titles.$serviceTypeId", ''));
                $serviceTitle = $typedTitle !== '' ? $typedTitle : ($st->title ?? 'Service');
                $pricingType        = (string) $request->input("pricing_types.$serviceTypeId", 'fixed');
                $servicePrice       = $pricingType === 'quote' ? null : $request->input("prices.$serviceTypeId");
                $duration           = $request->input("durations.$serviceTypeId");
                $serviceLocation    = $request->input("service_locations.$serviceTypeId");
                $availability       = $request->input("availabilities.$serviceTypeId");
                $minNotice          = $availability === 'appointment' ? $request->input("min_notices.$serviceTypeId") : null;
                $serviceDescription = $request->input("descriptions.$serviceTypeId");
                $files              = $request->file("images_by_type.$serviceTypeId", []) ?? [];
            }

            $gallery = [];
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $gallery[] = $file->store('service-gallery', 'public');
                }
            }

            $service = Service::create([
                'artisan_id'       => $user->id,
                'category_id'      => $base['category_id'],
                'service_type_id'  => $serviceTypeId,
                'provider_name'    => $user->name,
                'profession'       => $user->profession ?? 'Artisan',
                'city'             => $base['location'],
                'phone_number'     => $user->phone ?? '—',
                'title'            => $serviceTitle,
                'pricing_type'     => $pricingType,
                'price'            => $servicePrice,
                'duration'         => $duration,
                'service_location' => $serviceLocation,
                'availability'     => $availability,
                'min_notice'       => $minNotice,
                'description'      => $serviceDescription,
                'location'         => $base['location'],
                'service_image'    => $gallery[0] ?? null,
                'gallery_images'   => $gallery,
                'images'           => $gallery,
                'status'           => 'active',
                'is_verified'      => true,
            ]);

            $createdServices[] = $service;
        }

        // Notify Admins
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id'      => $admin->id,
                'type'         => 'service_created',
                'related_type' => 'service',
                'related_id'   => $createdServices[0]->id,
                'title'        => 'Nouveau(x) service(s) publié(s)',
                'message'      => "{$user->name} a publié " . count($createdServices) . " service(s).",
                'action_url'   => route('admin.services.index'),
                'is_read'      => false,
            ]);
        }

        $msg = count($createdServices) > 1
            ? count($createdServices) . ' services publiés avec succès.'
            : 'Votre service a été publié avec succès.';

        return redirect()->route('user.services.my')->with('success', $msg);
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

        $durationsList      = implode(',', array_keys(Service::DURATIONS));
        $availabilitiesList = implode(',', array_keys(Service::AVAILABILITIES));
        $minNoticesList     = implode(',', array_keys(Service::MIN_NOTICES));

        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'category_id'      => ['required', 'exists:categories,id'],
            'service_type_id'  => ['nullable', 'exists:service_types,id'],
            'pricing_type'     => ['required', 'in:fixed,starting_from,quote'],
            'price'            => ['nullable', 'numeric', 'min:0', 'required_unless:pricing_type,quote'],
            'duration'         => ['required', "in:{$durationsList}"],
            'service_location' => ['required', 'in:home,provider,both'],
            'availability'     => ['required', "in:{$availabilitiesList}"],
            'min_notice'       => ['nullable', 'required_if:availability,appointment', "in:{$minNoticesList}"],
            'description'      => ['nullable', 'string'],
            'location'         => ['required', 'string', 'max:255'],
            'status'           => ['required', 'in:active,inactive'],
            'service_image'    => ['nullable', 'image'],
            'images.*'         => ['nullable', 'image'],
        ]);

        // Si "Sur devis", le prix doit impérativement être null en base (ne jamais conserver l'ancien prix)
        if ($validated['pricing_type'] === 'quote') {
            $validated['price'] = null;
        }

        // Si disponibilité n'est pas "appointment", min_notice doit être null
        if ($validated['availability'] !== 'appointment') {
            $validated['min_notice'] = null;
        }

        $user = Auth::user();

        // Si le service passe de inactif à actif, on vérifie la limite
        if ($validated['status'] === 'active' && $service->status !== 'active') {
            $activeSubscription = $user->activeSubscription;
            $maxServices = $activeSubscription && $activeSubscription->subscriptionPlan
                ? $activeSubscription->subscriptionPlan->max_services
                : 1;

            $currentServicesCount = $user->services()->active()->count();

            if ($currentServicesCount >= $maxServices) {
                return back()->with('error', "Vous avez atteint la limite de {$maxServices} service(s) actif(s) pour votre abonnement actuel. Désactivez un autre service ou passez à un plan supérieur.")
                             ->with('upgrade_url', route('user.subscription.index'))
                             ->withInput();
            }
        }

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
