<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\JobOffer;
use Illuminate\View\View;

class RecruiterProfileController extends Controller
{
    /**
     * Display the recruiter's public profile and their active job offers.
     */
    public function show(int $id): View
    {
        $recruiter = User::whereIn('user_type', ['recruiter', 'job_seeker'])
            ->where('id', $id)
            ->firstOrFail();

        $jobOffers = JobOffer::active()
            ->notExpired()
            ->where('employer_id', $recruiter->id)
            ->latest()
            ->paginate(10);

        $stats = [
            'active_offers' => JobOffer::active()->notExpired()->where('employer_id', $recruiter->id)->count(),
            'member_since'  => $recruiter->created_at ? $recruiter->created_at->format('M Y') : 'N/A',
        ];

        return view('user.recruiter-profile', compact('recruiter', 'jobOffers', 'stats'));
    }
}
