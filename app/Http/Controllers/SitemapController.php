<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate the sitemap XML for SEO.
     */
    public function index(): Response
    {
        try {
            $services = Service::where('status', 'active')->latest()->get();
            $jobs     = JobOffer::active()->notExpired()->latest()->get();
            $artisans = User::where('user_type', 'artisan')->where('status', 'active')->latest()->get();

            $content = view('sitemap', compact('services', 'jobs', 'artisans'))->render();

            return response($content, 200)
                ->header('Content-Type', 'text/xml');
        } catch (\Throwable $e) {
            \Log::error('Erreur génération sitemap: ' . $e->getMessage());
            return response('<xml><error>Erreur sitemap</error></xml>', 500)
                ->header('Content-Type', 'text/xml');
        }
    }
}
