<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cv;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CvController extends Controller
{
    /**
     * Show the CV page (upload focus).
     */
    public function index(): View
    {
        $user = Auth::user();
        $cv   = $user->cv;

        return view('user.cv.index', compact('cv'));
    }

    /**
     * Simple update for CV information (if needed).
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'phone'   => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        Cv::updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name'    => $user->name,
                'email'        => $user->email,
                'phone_number' => $validated['phone'],
                'address'      => $validated['address'],
            ]
        );

        return back()->with('success', 'Informations mises à jour.');
    }

    /**
     * Delete the user's CV (record and file).
     */
    public function destroy(): RedirectResponse
    {
        $user = Auth::user();
        if ($user->cv) {
            // Delete file if exists
            if ($user->cv->cv_file) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->cv->cv_file);
            }
            $user->cv->delete();
        }

        return back()->with('success', 'CV supprimé avec succès.');
    }
}
