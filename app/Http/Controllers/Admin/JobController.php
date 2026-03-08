<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobOffer;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        $jobs = JobOffer::withCount('applications')->latest()->paginate(10);
        return view('admin.jobs.index', compact('jobs'));
    }

    public function create()
    {
        return view('admin.jobs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'logo_url' => 'nullable|url',
            'location' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'contract_type' => 'required|string|in:Full-time,Part-time,Freelance',
            'salary_range' => 'nullable|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'status' => 'required|in:active,expired',
        ]);

        $validated['user_id'] = auth()->id();

        JobOffer::create($validated);

        return redirect()->route('admin.jobs.index')->with('success', 'Offre d\'emploi publiée avec succès.');
    }

    public function edit(JobOffer $job)
    {
        return view('admin.jobs.edit', compact('job'));
    }

    public function update(Request $request, JobOffer $job)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'logo_url' => 'nullable|url',
            'location' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'contract_type' => 'required|string|in:Full-time,Part-time,Freelance',
            'salary_range' => 'nullable|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'status' => 'required|in:active,expired',
        ]);

        $job->update($validated);

        return redirect()->route('admin.jobs.index')->with('success', 'Offre d\'emploi mise à jour avec succès.');
    }

    public function destroy(JobOffer $job)
    {
        $job->delete();
        return redirect()->route('admin.jobs.index')->with('success', 'Offre d\'emploi supprimée avec succès.');
    }
}
