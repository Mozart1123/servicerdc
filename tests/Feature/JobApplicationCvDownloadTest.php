<?php

namespace Tests\Feature;

use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class JobApplicationCvDownloadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('role')->default('user');
            $table->string('user_type')->default('client');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('employer_id');
            $table->string('title');
            $table->string('company_name');
            $table->string('location')->nullable();
            $table->string('category')->nullable();
            $table->string('contract_type')->nullable();
            $table->string('salary_range')->nullable();
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_offer_id');
            $table->foreignId('user_id');
            $table->string('status')->default('pending');
            $table->text('message')->nullable();
            $table->string('cv_attachment')->nullable();
            $table->timestamps();
        });
    }

    public function test_applicant_is_redirected_when_cv_file_is_missing(): void
    {
        $applicant = User::create([
            'name' => 'Candidat',
            'email' => 'candidate@example.com',
            'password' => 'password',
            'role' => User::ROLE_USER,
            'user_type' => User::TYPE_CLIENT,
        ]);

        $employer = User::create([
            'name' => 'Employeur',
            'email' => 'employer@example.com',
            'password' => 'password',
            'role' => User::ROLE_USER,
            'user_type' => User::TYPE_RECRUITER,
        ]);

        $jobOffer = JobOffer::create([
            'user_id' => $employer->id,
            'employer_id' => $employer->id,
            'title' => 'Développeur backend',
            'company_name' => 'Acme',
            'location' => 'Kinshasa',
            'category' => 'informatique',
            'contract_type' => 'full_time',
            'status' => 'active',
            'description' => 'Description',
            'requirements' => 'Exigences',
            'salary_range' => '1000-2000',
        ]);

        $application = JobApplication::create([
            'job_offer_id' => $jobOffer->id,
            'user_id' => $applicant->id,
            'cv_attachment' => 'missing/cv.pdf',
            'status' => JobApplication::STATUS_PENDING,
            'message' => 'Bonjour',
        ]);

        $this->actingAs($applicant)
            ->from(route('user.applications.index'))
            ->get(route('user.applications.download-cv', ['id' => $application->id]))
            ->assertRedirect()
            ->assertSessionHas('error', 'Le fichier CV est introuvable sur le serveur.');
    }
}
