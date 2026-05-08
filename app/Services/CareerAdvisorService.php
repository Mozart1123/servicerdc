<?php

namespace App\Services;

use App\Models\CareerPath;
use App\Models\CareerRecommendation;
use App\Models\User;
use App\Models\JobOffer;

class CareerAdvisorService
{
    /**
     * Generate career recommendations for a user.
     */
    public function generateRecommendations(User $user, bool $syncBefore = false)
    {
        if ($syncBefore) {
            $this->syncUserSkillsFromActivity($user);
            $user->refresh();
        }

        $userSkills = $user->skills ?? [];
        $userInterests = $user->interests ?? [];

        if (empty($userSkills) && empty($userInterests)) {
            return collect();
        }

        $careerPaths = CareerPath::all();
        $recommendations = [];

        foreach ($careerPaths as $path) {
            $score = $this->calculateMatchScore($userSkills, $userInterests, $path);

            if ($score > 15) { // Lower threshold for more results
                $recommendations[] = [
                    'career_path' => $path,
                    'score' => $score,
                    'analysis' => $this->generateAnalysis($userSkills, $userInterests, $path, $score),
                ];
            }
        }

        // Sort by score descending
        usort($recommendations, fn($a, $b) => $b['score'] <=> $a['score']);

        // Save to database
        $this->saveRecommendations($user, $recommendations);

        return collect($recommendations);
    }

    /**
     * Automatically extract and sync skills based on user's platform activity.
     */
    public function syncUserSkillsFromActivity(User $user)
    {
        $extractedSkills = [];

        // 1. Scan Job Applications
        $applications = $user->jobApplications()
            ->with('jobOffer')
            ->get();

        foreach ($applications as $app) {
            $text = ($app->jobOffer->title ?? '') . ' ' . ($app->jobOffer->description ?? '') . ' ' . ($app->message ?? '');
            $extractedSkills = array_merge($extractedSkills, $this->extractKeywords($text));
        }

        // 2. Scan Services (if artisan)
        $services = $user->services()->get();
        foreach ($services as $service) {
            $text = $service->title . ' ' . $service->description;
            $extractedSkills = array_merge($extractedSkills, $this->extractKeywords($text));
        }

        // 3. Scan Documents (Simulation of CV parsing)
        $documents = $user->documents()->where('type', 'cv')->get();
        foreach ($documents as $doc) {
            // In a real app, we'd use OCR/PDF parsing here. 
            // For now, we simulate extraction from metadata or status.
            if ($doc->ai_status === 'processed') {
                // Simulation: if processed, we assume some skills were found
                $extractedSkills[] = 'Documentation';
            }
        }

        // Merge with existing and unique
        $currentSkills = $user->skills ?? [];
        $finalSkills = array_unique(array_merge($currentSkills, $extractedSkills));

        $user->update(['skills' => array_values($finalSkills)]);

        return $finalSkills;
    }

    /**
     * Helper to extract predefined keywords from text.
     */
    private function extractKeywords(string $text)
    {
        $availableSkills = [
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

        $found = [];
        foreach ($availableSkills as $skill) {
            if (stripos($text, $skill) !== false) {
                $found[] = $skill;
            }
        }

        return $found;
    }

    /**
     * Calculate match score based on skills and interests.
     */
    private function calculateMatchScore(array $userSkills, array $userInterests, CareerPath $path)
    {
        $pathSkills = $path->required_skills;
        $pathInterests = $path->interests_match;

        $skillMatchCount = count(array_intersect(
            array_map('strtolower', $userSkills),
            array_map('strtolower', $pathSkills)
        ));

        $interestMatchCount = count(array_intersect(
            array_map('strtolower', $userInterests),
            array_map('strtolower', $pathInterests)
        ));

        $skillScore = count($pathSkills) > 0 ? ($skillMatchCount / count($pathSkills)) * 70 : 0; // Increased weight for skills
        $interestScore = count($pathInterests) > 0 ? ($interestMatchCount / count($pathInterests)) * 30 : 0;

        return round($skillScore + $interestScore, 2);
    }

    /**
     * Generate a brief analysis for the user.
     */
    private function generateAnalysis(array $userSkills, array $userInterests, CareerPath $path, float $score)
    {
        $commonSkills = array_intersect(
            array_map('strtolower', $userSkills),
            array_map('strtolower', $path->required_skills)
        );

        $missingSkills = array_diff(
            array_map('strtolower', $path->required_skills),
            array_map('strtolower', $userSkills)
        );

        $analysis = "Basé sur votre activité récente sur la plateforme, nous avons identifié une correspondance de " . round($score) . "% pour le poste de " . $path->title . ". ";

        if (!empty($commonSkills)) {
            $analysis .= "Votre expérience en " . implode(', ', array_slice($commonSkills, 0, 3)) . " détectée dans vos interactions est un atout majeur. ";
        }

        if (!empty($missingSkills)) {
            $analysis .= "Pour parfaire votre profil, envisagez de renforcer vos compétences en : " . implode(', ', array_slice($missingSkills, 0, 2)) . ".";
        }

        return $analysis;
    }

    /**
     * Save recommendations to the database.
     */
    private function saveRecommendations(User $user, array $recommendations)
    {
        // Clear old recommendations
        CareerRecommendation::where('user_id', $user->id)->delete();

        foreach (array_slice($recommendations, 0, 5) as $rec) {
            CareerRecommendation::create([
                'user_id' => $user->id,
                'career_path_id' => $rec['career_path']->id,
                'match_score' => $rec['score'],
                'analysis' => $rec['analysis'],
            ]);
        }
    }

    /**
     * Get related job offers for a career path.
     */
    public function getRelatedJobOffers(CareerPath $path)
    {
        // Simple search by title keywords
        $keywords = explode(' ', $path->title);

        return JobOffer::where(function ($query) use ($keywords) {
            foreach ($keywords as $word) {
                if (strlen($word) > 2) {
                    $query->orWhere('title', 'LIKE', "%$word%")
                        ->orWhere('description', 'LIKE', "%$word%");
                }
            }
        })->take(5)->get();
    }
}
