<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add profile_photo to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_photo')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('profile_photo');
            }
        });

        // Add image fields to services
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'service_image')) {
                $table->string('service_image')->nullable()->after('images');
            }
            if (!Schema::hasColumn('services', 'gallery_images')) {
                $table->json('gallery_images')->nullable()->after('service_image');
            }
        });

        // Add image fields to job_offers
        Schema::table('job_offers', function (Blueprint $table) {
            if (!Schema::hasColumn('job_offers', 'company_logo')) {
                $table->string('company_logo')->nullable()->after('logo_url');
            }
            if (!Schema::hasColumn('job_offers', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('company_logo');
            }
        });

        // Add photo/file fields to CVs
        Schema::table('cvs', function (Blueprint $table) {
            if (!Schema::hasColumn('cvs', 'profile_photo')) {
                $table->string('profile_photo')->nullable()->after('portfolio_link');
            }
            if (!Schema::hasColumn('cvs', 'cv_file')) {
                $table->string('cv_file')->nullable()->after('profile_photo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_photo', 'bio']);
        });
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['service_image', 'gallery_images']);
        });
        Schema::table('job_offers', function (Blueprint $table) {
            $table->dropColumn(['company_logo', 'cover_image']);
        });
        Schema::table('cvs', function (Blueprint $table) {
            $table->dropColumn(['profile_photo', 'cv_file']);
        });
    }
};
