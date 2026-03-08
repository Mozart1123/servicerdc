<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    /**
     * Display all job applications (MOSALA+ Integration)
     */
    public function index()
    {
        $applications = JobApplication::with(['job', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.job-applications.index', compact('applications'));
    }

    /**
     * Update the status of a job application.
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,accepted',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $application = JobApplication::with(['user', 'jobOffer'])->findOrFail($id);
        
        $application->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_at' => now(),
        ]);

        // Create notification for user
        \App\Models\Notification::create([
            'user_id' => $application->user_id,
            'type' => 'application_status_updated',
            'title' => 'Candidature mise à jour',
            'message' => "Votre candidature pour '{$application->jobOffer->title}' a été " . 
                        ($request->status === 'accepted' || $request->status === 'approved' ? 'acceptée' : 
                        ($request->status === 'rejected' ? 'rejetée' : 'mise à jour')),
            'data' => [
                'job_id' => $application->job_offer_id,
                'application_id' => $application->id,
                'status' => $request->status,
            ],
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour avec succès.',
                'new_status' => $application->status,
            ]);
        }

        return back()->with('success', 'Le statut de la candidature a été mis à jour.');
    }
}
