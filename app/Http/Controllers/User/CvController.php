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
     * 7 MCQ questions for the CV template builder.
     */
    private const MCQ_QUESTIONS = [
        [
            'key'      => 'experience_level',
            'question' => 'Quel est votre niveau d\'experience ?',
            'options'  => ['Debutant (0-1 an)', 'Junior (1-3 ans)', 'Confirme (3-5 ans)', 'Senior (5-10 ans)', 'Expert (10+ ans)'],
        ],
        [
            'key'      => 'availability',
            'question' => 'Quelle est votre disponibilite ?',
            'options'  => ['Temps plein', 'Temps partiel', 'Freelance', 'Stage', 'En recherche'],
        ],
        [
            'key'      => 'preferred_sector',
            'question' => 'Quel secteur vous interesse le plus ?',
            'options'  => ['Technologie / IT', 'Construction / Batiment', 'Commerce / Vente', 'Sante / Social', 'Education / Formation', 'Transport / Logistique', 'Artisanat / Metiers manuels'],
        ],
        [
            'key'      => 'education_level',
            'question' => 'Quel est votre niveau d\'etudes ?',
            'options'  => ['Primaire', 'Secondaire', 'Baccalaureat', 'Licence (Bac+3)', 'Master (Bac+5)', 'Doctorat / PhD', 'Formation professionnelle'],
        ],
        [
            'key'      => 'languages_spoken',
            'question' => 'Quelles langues parlez-vous couramment ?',
            'options'  => ['Francais uniquement', 'Francais + Lingala', 'Francais + Swahili', 'Francais + Anglais', 'Francais + Tshiluba', 'Multilingue (3+)', 'Autre'],
        ],
        [
            'key'      => 'salary_expectation',
            'question' => 'Quelle est votre attente salariale mensuelle ?',
            'options'  => ['Moins de 200$', '200$ - 500$', '500$ - 1000$', '1000$ - 2000$', '2000$ - 5000$', 'Plus de 5000$', 'Negociable'],
        ],
        [
            'key'      => 'key_strength',
            'question' => 'Quelle est votre force principale ?',
            'options'  => ['Travail en equipe', 'Autonomie', 'Leadership', 'Creativite', 'Rigueur / Organisation', 'Adaptabilite', 'Communication'],
        ],
    ];

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
     * Show the CV template builder with 7 MCQ questions.
     */
    public function create(): View
    {
        $user = Auth::user();
        $cv = $user->cv;
        $questions = self::MCQ_QUESTIONS;

        return view('user.cv.index', compact('cv', 'questions'));
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
                'phone_number' => $validated['phone'] ?? '',
                'address'      => $validated['address'] ?? '',
                'education'    => '',
                'skills'       => '',
                'experience'   => '',
                'languages'    => '',
            ]
        );

        return back()->with('success', 'Informations mises a jour.');
    }

    /**
     * Save the CV template builder answers.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'full_name'    => 'nullable|string|max:255',
            'job_title'    => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:30',
            'address'      => 'nullable|string|max:255',
            'summary'      => 'nullable|string|max:1000',
            'experience_level'   => 'nullable|string|max:255',
            'availability'       => 'nullable|string|max:255',
            'preferred_sector'   => 'nullable|string|max:255',
            'education_level'    => 'nullable|string|max:255',
            'languages_spoken'   => 'nullable|string|max:255',
            'salary_expectation' => 'nullable|string|max:255',
            'key_strength'       => 'nullable|string|max:255',
        ]);

        $answers = [];
        foreach (self::MCQ_QUESTIONS as $q) {
            $answers[$q['key']] = $validated[$q['key']] ?? null;
        }

        $cv = Cv::updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name'        => $validated['full_name'] ?? $user->name,
                'email'            => $user->email,
                'phone_number'     => $validated['phone'] ?? '',
                'address'          => $validated['address'] ?? '',
                'education'        => '',
                'skills'           => '',
                'experience'       => '',
                'languages'        => '',
                'job_title'        => $validated['job_title'] ?? '',
                'summary'          => $validated['summary'] ?? '',
                'template_answers' => $answers,
            ]
        );

        return back()->with('success', 'CV mis a jour avec succes !');
    }

    /**
     * Handle CV file upload.
     */
    public function fileUpload(Request $request): RedirectResponse
    {
        $request->validate([
            'cv_file' => 'required|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:5120',
        ]);

        $user = Auth::user();

        if ($user->cv && $user->cv->cv_file) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->cv->cv_file);
        }

        $path = $request->file('cv_file')->store('cv', 'public');

        Cv::updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name'    => $user->name,
                'email'        => $user->email,
                'phone_number' => $user->phone ?? '',
                'address'      => $user->address ?? '',
                'education'    => '',
                'skills'       => '',
                'experience'   => '',
                'languages'    => '',
                'cv_file'      => $path,
            ]
        );

        return back()->with('success', 'CV importe avec succes !');
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
