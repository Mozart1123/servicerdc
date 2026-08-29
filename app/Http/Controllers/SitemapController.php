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

            $urls = collect();

            // Static pages
            $urls->push(['loc' => route('home'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'daily', 'priority' => '1.0']);
            $urls->push(['loc' => route('public.services.index'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'daily', 'priority' => '0.9']);
            $urls->push(['loc' => route('public.jobs.index'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'daily', 'priority' => '0.9']);
            $urls->push(['loc' => route('public.artisans.index'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'daily', 'priority' => '0.9']);
            $urls->push(['loc' => route('about'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.5']);
            $urls->push(['loc' => route('contact'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.5']);

            // Dynamic content
            foreach ($services as $service) {
                $urls->push([
                    'loc' => route('public.services.show', $service->id),
                    'lastmod' => optional($service->updated_at)->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ]);
            }

            foreach ($jobs as $job) {
                $urls->push([
                    'loc' => route('public.jobs.show', $job->id),
                    'lastmod' => optional($job->updated_at)->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ]);
            }

            foreach ($artisans as $artisan) {
                $urls->push([
                    'loc' => route('public.artisans.show', $artisan->id),
                    'lastmod' => optional($artisan->updated_at)->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                ]);
            }

            $content = view('sitemap', compact('urls'))->render();

            return response($content, 200)
                ->header('Content-Type', 'text/xml');
        } catch (\Throwable $e) {
            \Log::error('Erreur génération sitemap: ' . $e->getMessage());
            return response('<xml><error>Erreur sitemap</error></xml>', 500)
                ->header('Content-Type', 'text/xml');
        }
    }
}
