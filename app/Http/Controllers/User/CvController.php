<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cv;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CvController extends Controller
{
    private const DRC_PROVINCES = [
        'Bas-Uele', 'Bas-Congo', 'Bandundu', 'Équateur', 'Haut-Katanga', 'Haut-Lomami', 'Haut-Uele',
        'Ituri', 'Kasaï', 'Kasaï Central', 'Kasaï Oriental', 'Kinshasa', 'Kwango', 'Kwilu',
        'Lomami', 'Lualaba', 'Mai-Ndombe', 'Maniema', 'Mongala', 'Nord-Kivu', 'Nord-Ubangi',
        'Sankuru', 'Sud-Kivu', 'Sud-Ubangi', 'Tanganyika', 'Tshopo', 'Tshuapa',
    ];

    /**
     * Show the CV page (upload focus).
     */
    public function index(): View
    {
        $user                = Auth::user();
        $cv                  = $user->cv;
        $returnTo            = request('return_to');
        $provinces           = self::DRC_PROVINCES;
        $experienceEntries   = $this->getExperienceEntries($cv);
        $educationEntries    = $this->getEducationEntries($cv);
        $skills             = $this->getSkills($cv);
        $languages          = $this->getLanguages($cv);

        return view('user.cv.index', compact('cv', 'returnTo', 'provinces', 'experienceEntries', 'educationEntries', 'skills', 'languages'));
    }

    /**
     * Show the full CV page.
     */
    public function create(): View
    {
        $user                = Auth::user();
        $cv                  = $user->cv;
        $returnTo            = request('return_to');
        $provinces           = self::DRC_PROVINCES;
        $experienceEntries   = $this->getExperienceEntries($cv);
        $educationEntries    = $this->getEducationEntries($cv);
        $skills             = $this->getSkills($cv);
        $languages          = $this->getLanguages($cv);

        return view('user.cv.index', compact('cv', 'returnTo', 'provinces', 'experienceEntries', 'educationEntries', 'skills', 'languages'));
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->saveCv($request);
    }

    /**
     * Save the full CV.
     */
    public function update(Request $request): RedirectResponse
    {
        return $this->saveCv($request);
    }

    protected function saveCv(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'full_name'                 => ['required', 'string', 'max:255'],
            'job_title'                 => ['required', 'string', 'max:255'],
            'phone'                     => ['required', 'string', 'regex:/^(\+243|0)\d{9}$/'],
            'province'                  => ['required', 'string', Rule::in(self::DRC_PROVINCES)],
            'address'                   => ['required', 'string', 'max:255'],
            'summary'                   => ['nullable', 'string', 'max:1200'],
            'experience'                => ['nullable', 'array'],
            'experience.*.job_title'    => ['nullable', 'string', 'max:255'],
            'experience.*.company'      => ['nullable', 'string', 'max:255'],
            'experience.*.start_date'   => ['nullable', 'string', 'max:100'],
            'experience.*.end_date'     => ['nullable', 'string', 'max:100'],
            'experience.*.description'  => ['nullable', 'string', 'max:2000'],
            'education'                 => ['nullable', 'array'],
            'education.*.school'        => ['nullable', 'string', 'max:255'],
            'education.*.degree'        => ['nullable', 'string', 'max:255'],
            'education.*.start_date'    => ['nullable', 'string', 'max:100'],
            'education.*.end_date'      => ['nullable', 'string', 'max:100'],
            'education.*.description'   => ['nullable', 'string', 'max:2000'],
            'skills'                    => ['nullable', 'array'],
            'skills.*'                  => ['nullable', 'string', 'max:100'],
            'languages'                 => ['nullable', 'array'],
            'languages.*.language'      => ['nullable', 'string', 'max:100'],
            'languages.*.level'         => ['nullable', 'string', Rule::in(['Débutant', 'Intermédiaire', 'Excellent'])],
            'cv_file'                   => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'diploma_file'              => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'return_to'                 => ['nullable', 'url'],
        ]);

        $cvData = [
            'full_name'    => $validated['full_name'],
            'email'        => $user->email,
            'phone_number' => $validated['phone'],
            'province'     => $validated['province'],
            'address'      => $validated['address'],
            'job_title'    => $validated['job_title'],
            'summary'      => $validated['summary'] ?? '',
            'experience'   => $this->normalizeExperience($validated['experience'] ?? []),
            'education'    => $this->normalizeEducation($validated['education'] ?? []),
            'skills'       => $this->normalizeSkills($validated['skills'] ?? []),
            'languages'    => $this->normalizeLanguages($validated['languages'] ?? []),
        ];

        $cv = Cv::updateOrCreate(['user_id' => $user->id], $cvData);

        if ($request->hasFile('cv_file')) {
            if ($cv->cv_file) {
                Storage::disk('public')->delete($cv->cv_file);
            }
            $cv->cv_file = $request->file('cv_file')->store('cv_files', 'public');
        }

        if ($request->hasFile('diploma_file')) {
            if ($cv->diploma_file) {
                Storage::disk('public')->delete($cv->diploma_file);
            }
            $cv->diploma_file = $request->file('diploma_file')->store('cv_diplomas', 'public');
        }

        $cv->save();

        if (!empty($validated['return_to'])) {
            return redirect()->to($validated['return_to'])->with('success', 'Votre CV a été complété. Vous pouvez maintenant postuler.');
        }

        return back()->with('success', 'CV mis à jour avec succès !');
    }

    private function normalizeExperience(array $experience): array
    {
        return array_values(array_filter(array_map(function ($item) {
            $jobTitle = trim($item['job_title'] ?? '');
            $company = trim($item['company'] ?? '');
            $startDate = trim($item['start_date'] ?? '');
            $endDate = trim($item['end_date'] ?? '');
            $description = trim($item['description'] ?? '');

            if ($jobTitle === '' && $company === '' && $startDate === '' && $endDate === '' && $description === '') {
                return null;
            }

            return [
                'job_title'   => $jobTitle,
                'company'     => $company,
                'start_date'  => $startDate,
                'end_date'    => $endDate,
                'description' => $description,
            ];
        }, $experience), fn ($item) => $item !== null));
    }

    private function normalizeEducation(array $education): array
    {
        return array_values(array_filter(array_map(function ($item) {
            $school = trim($item['school'] ?? '');
            $degree = trim($item['degree'] ?? '');
            $startDate = trim($item['start_date'] ?? '');
            $endDate = trim($item['end_date'] ?? '');
            $description = trim($item['description'] ?? '');

            if ($school === '' && $degree === '' && $startDate === '' && $endDate === '' && $description === '') {
                return null;
            }

            return [
                'school'      => $school,
                'degree'      => $degree,
                'start_date'  => $startDate,
                'end_date'    => $endDate,
                'description' => $description,
            ];
        }, $education), fn ($item) => $item !== null));
    }

    private function normalizeSkills(array $skills): array
    {
        return array_values(array_filter(array_map('trim', $skills), fn ($skill) => $skill !== ''));
    }

    private function normalizeLanguages(array $languages): array
    {
        return array_values(array_filter(array_map(function ($item) {
            $language = trim($item['language'] ?? '');
            $level = trim($item['level'] ?? 'Débutant');

            if ($language === '') {
                return null;
            }

            return [
                'language' => $language,
                'level'    => in_array($level, ['Débutant', 'Intermédiaire', 'Excellent']) ? $level : 'Débutant',
            ];
        }, $languages), fn ($item) => $item !== null));
    }

    private function getExperienceEntries(?Cv $cv): array
    {
        $entries = old('experience', []);

        if (!empty($entries)) {
            return $entries;
        }

        return $cv && is_array($cv->experience) ? $cv->experience : [
            ['job_title' => '', 'company' => '', 'start_date' => '', 'end_date' => '', 'description' => ''],
        ];
    }

    private function getEducationEntries(?Cv $cv): array
    {
        $entries = old('education', []);

        if (!empty($entries)) {
            return $entries;
        }

        return $cv && is_array($cv->education) ? $cv->education : [
            ['school' => '', 'degree' => '', 'start_date' => '', 'end_date' => '', 'description' => ''],
        ];
    }

    private function getSkills(?Cv $cv): array
    {
        $skills = old('skills', []);

        if (!empty($skills)) {
            return $skills;
        }

        return $cv && is_array($cv->skills) ? $cv->skills : [];
    }

    private function getLanguages(?Cv $cv): array
    {
        $languages = old('languages', []);

        if (!empty($languages)) {
            return $languages;
        }

        return $cv && is_array($cv->languages) ? $cv->languages : [
            ['language' => '', 'level' => 'Débutant'],
        ];
    }

    private function linesToArray(string $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $value)), fn ($line) => $line !== ''));
    }

    /**
     * Handle CV file upload.
     */
    public function fileUpload(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cv_file'   => ['required', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
            'return_to' => ['nullable', 'url'],
        ]);

        $user = Auth::user();
        $cv   = $user->cv ?? new Cv(['user_id' => $user->id]);

        if ($cv->cv_file) {
            Storage::disk('public')->delete($cv->cv_file);
        }

        $cv->cv_file = $request->file('cv_file')->store('cv_files', 'public');
        $cv->full_name    = $cv->full_name ?? $user->name;
        $cv->email        = $user->email;
        $cv->phone_number = $cv->phone_number ?? $user->phone;
        $cv->address      = $cv->address ?? '';
        $cv->save();

        if (!empty($validated['return_to'])) {
            return redirect()->to($validated['return_to'])->with('success', 'Votre CV a été complété. Vous pouvez maintenant postuler.');
        }

        return back()->with('success', 'CV importé avec succès !');
    }

    /**
     * Delete the user's CV (record and file).
     */
    public function destroy(): RedirectResponse
    {
        $user = Auth::user();
        if ($user->cv) {
            if ($user->cv->cv_file) {
                Storage::disk('public')->delete($user->cv->cv_file);
            }
            if ($user->cv->diploma_file) {
                Storage::disk('public')->delete($user->cv->diploma_file);
            }
            $user->cv->delete();
        }

        return back()->with('success', 'CV supprimé avec succès.');
    }
}
