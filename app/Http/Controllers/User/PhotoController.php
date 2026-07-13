<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\JobOffer;
use App\Models\Cv;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    private const IMAGE_RULES = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
    private const CV_RULES    = ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'];

    /**
     * Update the authenticated user's profile photo.
     * POST /profile/photo
     */
    public function updateProfilePhoto(Request $request): RedirectResponse
    {
        $request->validate(['photo' => self::IMAGE_RULES]);

        $user = Auth::user();

        if ($request->hasFile('photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->update(['profile_photo' => $path]);
        }

        return back()->with('success', 'Photo de profil mise à jour.');
    }

    /**
     * Update a service image.
     * POST /services/{service}/image
     */
    public function updateServiceImage(Request $request, Service $service): RedirectResponse
    {
        $request->validate(['image' => self::IMAGE_RULES]);

        if ($service->artisan_id !== Auth::id()) { abort(403); }

        if ($request->hasFile('image')) {
            if ($service->service_image) {
                Storage::disk('public')->delete($service->service_image);
            }
            $path = $request->file('image')->store('service-images', 'public');
            $service->update(['service_image' => $path]);
        }

        return back()->with('success', 'Image principale du service mise à jour.');
    }

    /**
     * Update a service gallery (multiple images).
     * POST /services/{service}/gallery
     */
    public function updateServiceGallery(Request $request, Service $service): RedirectResponse
    {
        $request->validate([
            'gallery.*' => self::IMAGE_RULES
        ]);

        if ($service->artisan_id !== Auth::id()) { abort(403); }

        if ($request->hasFile('gallery')) {
            $gallery = is_array($service->gallery_images) ? $service->gallery_images : [];
            
            foreach ($request->file('gallery') as $file) {
                $gallery[] = $file->store('service-gallery', 'public');
            }
            
            $service->update(['gallery_images' => $gallery]);
        }

        return back()->with('success', 'Galerie photos mise à jour.');
    }

    /**
     * Delete a single gallery image.
     */
    public function deleteGalleryImage(Service $service, int $index): RedirectResponse
    {
        if ($service->artisan_id !== Auth::id()) { abort(403); }

        $gallery = $service->gallery_images;
        if (isset($gallery[$index])) {
            Storage::disk('public')->delete($gallery[$index]);
            unset($gallery[$index]);
            $service->update(['gallery_images' => array_values($gallery)]);
        }

        return back()->with('success', 'Image supprimée de la galerie.');
    }

    /**
     * Update a job offer logo.
     * POST /jobs/{job}/logo
     */
    public function updateJobLogo(Request $request, JobOffer $job): RedirectResponse
    {
        $request->validate(['logo' => self::IMAGE_RULES]);

        if ($job->employer_id !== Auth::id() && $job->user_id !== Auth::id()) { abort(403); }

        if ($request->hasFile('logo')) {
            if ($job->company_logo) {
                Storage::disk('public')->delete($job->company_logo);
            }
            $path = $request->file('logo')->store('job-logos', 'public');
            $job->update(['company_logo' => $path]);
        }

        return back()->with('success', 'Logo de l\'entreprise mis à jour.');
    }

    /**
     * Update a job offer cover image.
     * POST /jobs/{job}/cover
     */
    public function updateJobCover(Request $request, JobOffer $job): RedirectResponse
    {
        $request->validate(['cover' => self::IMAGE_RULES]);

        if ($job->employer_id !== Auth::id() && $job->user_id !== Auth::id()) { abort(403); }

        if ($request->hasFile('cover')) {
            if ($job->cover_image) {
                Storage::disk('public')->delete($job->cover_image);
            }
            $path = $request->file('cover')->store('job-covers', 'public');
            $job->update(['cover_image' => $path]);
        }

        return back()->with('success', 'Image de couverture mise à jour.');
    }

    /**
     * Update CV profile photo.
     * POST /cv/photo
     */
    public function updateCvPhoto(Request $request): RedirectResponse
    {
        $request->validate(['photo' => self::IMAGE_RULES]);

        $user = Auth::user();
        $cv   = $user->cv;

        if (!$cv) {
            $cv = Cv::create(['user_id' => $user->id, 'full_name' => $user->name, 'email' => $user->email]);
        }

        if ($request->hasFile('photo')) {
            if ($cv->profile_photo) {
                Storage::disk('public')->delete($cv->profile_photo);
            }
            $path = $request->file('photo')->store('cv-photos', 'public');
            $cv->update(['profile_photo' => $path]);
        }

        return back()->with('success', 'Photo du CV mise à jour.');
    }

    /**
     * Upload CV file (PDF/Word).
     * POST /cv/file
     */
    public function uploadCvFile(Request $request): RedirectResponse
    {
        $request->validate(['cv_file' => self::CV_RULES]);

        $user = Auth::user();
        $cv   = $user->cv;

        if (!$cv) {
            $cv = Cv::create(['user_id' => $user->id, 'full_name' => $user->name, 'email' => $user->email]);
        }

        if ($cv->cv_file) {
            Storage::disk('public')->delete($cv->cv_file);
        }

        $path = $request->file('cv_file')->store('cv-files', 'public');
        $cv->update(['cv_file' => $path, 'cv_file_path' => $path]);

        return back()->with('success', 'Le fichier CV a été téléversé avec succès.');
    }
}
