<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CareerPath;
use App\Models\CareerRecommendation;
use App\Services\CareerAdvisorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CareerAdvisorController extends Controller
{
    protected $advisorService;

    public function __construct(CareerAdvisorService $advisorService)
    {
        $this->advisorService = $advisorService;
    }

    /**
     * Display the index page with current recommendations.
     */
    public function index()
    {
        $user = Auth::user();

        // Auto-generate recommendations if none exist
        if (CareerRecommendation::where('user_id', $user->id)->count() === 0) {
            $this->advisorService->generateRecommendations($user, true);
        }

        // Get existing recommendations from DB
        $recommendations = CareerRecommendation::with('careerPath')
            ->where('user_id', $user->id)
            ->orderBy('match_score', 'desc')
            ->get();

        // Attach related jobs to each recommendation
        foreach ($recommendations as $rec) {
            $rec->jobs = $this->advisorService->getRelatedJobOffers($rec->careerPath);
        }

        // All available skills and interests for the selection UI (as fallback)
        $allSkills = [
            'PHP',
            'Laravel',
            'JavaScript',
            'Python',
            'Java',
            'C++',
            'SQL',
            'Figma',
            'React',
            'Vue',
            'HTML',
            'CSS',
            'Git',
            'Docker',
            'AWS',
            'Swift',
            'Kotlin',
            'Flutter',
            'Data Analysis',
            'Management',
            'Communication',
            'Design',
            'Marketing',
            'Accounting'
        ];

        $allInterests = [
            'Coding',
            'Design',
            'Management',
            'Problem Solving',
            'Creativity',
            'Technology',
            'Social Media',
            'Marketing',
            'Business',
            'Teaching',
            'Writing',
            'Research',
            'Mobile Apps'
        ];

        return view('user.career-advisor.index', compact('user', 'recommendations', 'allSkills', 'allInterests'));
    }

    /**
     * Synchronize skills from user activity and regenerate recommendations.
     */
    public function sync(Request $request)
    {
        $user = Auth::user();

        $this->advisorService->generateRecommendations($user, true);

        return redirect()->route('user.career-advisor.index')
            ->with('success', 'Votre profil a été analysé avec succès ! Les recommandations sont désormais basées sur vos activités récentes.');
    }

    /**
     * Update user profile manually (Fallback).
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'skills' => 'nullable|array',
            'interests' => 'nullable|array',
        ]);

        $user->update([
            'skills' => $request->skills ?? [],
            'interests' => $request->interests ?? [],
        ]);

        // Trigger ML recommendation engine
        $this->advisorService->generateRecommendations($user);

        return redirect()->route('user.career-advisor.index')
            ->with('success', 'Your career recommendations have been updated based on your new skills and interests!');
    }

    /**
     * Show details for a specific career path recommendation.
     */
    public function show(CareerPath $careerPath)
    {
        $user = Auth::user();
        $recommendation = CareerRecommendation::where('user_id', $user->id)
            ->where('career_path_id', $careerPath->id)
            ->first();

        // Get related job offers
        $jobs = $this->advisorService->getRelatedJobOffers($careerPath);

        return view('user.career-advisor.show', compact('careerPath', 'recommendation', 'jobs'));
    }
}
