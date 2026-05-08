<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CareerPathSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $careerPaths = [
            [
                'title' => 'Software Engineer',
                'description' => 'Develop, create, and modify general computer applications software or specialized utility programs.',
                'required_skills' => ['PHP', 'Laravel', 'MySQL', 'JavaScript', 'Git'],
                'interests_match' => ['Coding', 'Problem Solving', 'Technology', 'Logic'],
                'salary_range' => '$70k - $120k',
                'industry' => 'Technology',
            ],
            [
                'title' => 'Data Scientist',
                'description' => 'Develop and implement data sets, and then use them to help companies find patterns and solutions.',
                'required_skills' => ['Python', 'SQL', 'Statistics', 'Machine Learning', 'Data Visualization'],
                'interests_match' => ['Data Analysis', 'Math', 'Research', 'Patterns'],
                'salary_range' => '$80k - $140k',
                'industry' => 'Data & AI',
            ],
            [
                'title' => 'UX Designer',
                'description' => 'Focus on the interaction that users have with products like websites, apps, and physical objects.',
                'required_skills' => ['Figma', 'User Research', 'Prototyping', 'UI Design', 'Empathy'],
                'interests_match' => ['Design', 'User Experience', 'Creativity', 'Psychology'],
                'salary_range' => '$65k - $110k',
                'industry' => 'Design',
            ],
            [
                'title' => 'Mobile Developer',
                'description' => 'Design and build mobile applications for iOS and Android platforms.',
                'required_skills' => ['Flutter', 'React Native', 'Swift', 'Kotlin', 'REST APIs'],
                'interests_match' => ['Mobile Apps', 'Modern Tech', 'User Interface'],
                'salary_range' => '$75k - $125k',
                'industry' => 'Technology',
            ],
            [
                'title' => 'DevOps Engineer',
                'description' => 'Bridge the gap between developers and IT staff to ensure fast and reliable software delivery.',
                'required_skills' => ['Docker', 'Kubernetes', 'AWS', 'CI/CD', 'Linux'],
                'interests_match' => ['Automation', 'Infrastructure', 'Systems'],
                'salary_range' => '$85k - $150k',
                'industry' => 'Cloud Computing',
            ],
        ];

        foreach ($careerPaths as $path) {
            \App\Models\CareerPath::create($path);
        }
    }
}
