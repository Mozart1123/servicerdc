<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate dynamic sitemap.xml for Google & search engines.
     */
    public function index(): Response
    {
        $urls = [];
        $today = date('Y-m-d');

        // 1. Pages statiques publiques
        $staticPages = [
            ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => route('public.services.index'), 'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => route('public.jobs.index'), 'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => route('public.artisans.index'), 'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => route('about'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => route('contact'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => route('how-it-works'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => route('register'), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => route('login'), 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => route('privacy'), 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['loc' => route('terms'), 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['loc' => route('legal'), 'priority' => '0.3', 'changefreq' => 'yearly'],
        ];

        foreach ($staticPages as $page) {
            $urls[] = [
                'loc' => $page['loc'],
                'lastmod' => $today,
                'changefreq' => $page['changefreq'],
                'priority' => $page['priority'],
            ];
        }

        // 2. Offres d'emploi actives
        $jobs = JobOffer::where('status', 'active')
            ->where(function ($q) {
                $q->where('deadline', '>=', now())->orWhereNull('deadline');
            })
            ->latest('updated_at')
            ->get();

        foreach ($jobs as $job) {
            $urls[] = [
                'loc' => route('public.jobs.show', $job->id),
                'lastmod' => $job->updated_at ? $job->updated_at->format('Y-m-d') : $today,
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ];
        }

        // 3. Services actifs
        $services = Service::where('status', 'active')
            ->latest('updated_at')
            ->get();

        foreach ($services as $service) {
            $urls[] = [
                'loc' => route('public.services.show', $service->id),
                'lastmod' => $service->updated_at ? $service->updated_at->format('Y-m-d') : $today,
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ];
        }

        // 4. Artisans avec profil public actif
        $artisans = User::where('user_type', 'artisan')
            ->where('status', 'active')
            ->latest('updated_at')
            ->get();

        foreach ($artisans as $artisan) {
            $urls[] = [
                'loc' => route('public.artisans.show', $artisan->id),
                'lastmod' => $artisan->updated_at ? $artisan->updated_at->format('Y-m-d') : $today,
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ];
        }

        return response()
            ->view('sitemap', compact('urls'))
            ->header('Content-Type', 'text/xml');
    }
}
