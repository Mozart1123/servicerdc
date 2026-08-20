<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');

            DB::statement('ALTER TABLE users RENAME TO users_old');

            DB::statement("CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                name varchar NOT NULL,
                email varchar NOT NULL,
                email_verified_at datetime,
                password varchar NOT NULL,
                remember_token varchar,
                created_at datetime,
                updated_at datetime,
                phone varchar,
                user_type varchar check (user_type in ('client', 'artisan', 'job_seeker', 'recruiter')) not null default 'client',
                terms_accepted_at datetime,
                role varchar not null default 'user',
                status varchar not null default 'active',
                google_id varchar,
                facebook_id varchar,
                apple_id varchar,
                skills text,
                interests text,
                profile_photo varchar,
                bio text,
                province varchar,
                city varchar
            )");

            DB::statement("INSERT INTO users (
                id, name, email, email_verified_at, password, remember_token,
                created_at, updated_at, phone, user_type, terms_accepted_at,
                role, status, google_id, facebook_id, apple_id, skills, interests,
                profile_photo, bio, province, city
            )
            SELECT
                id, name, email, email_verified_at, password, remember_token,
                created_at, updated_at, phone, user_type, terms_accepted_at,
                role, status, google_id, facebook_id, apple_id, skills, interests,
                profile_photo, bio, province, city
            FROM users_old");

            DB::statement('UPDATE users SET user_type = "recruiter" WHERE user_type = "job_seeker"');
            DB::statement('DROP TABLE users_old');
            DB::statement('PRAGMA foreign_keys = ON');

            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('client', 'artisan', 'job_seeker', 'recruiter') DEFAULT 'client'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
            DB::statement('ALTER TABLE users RENAME TO users_reverted');

            DB::statement("CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                name varchar NOT NULL,
                email varchar NOT NULL,
                email_verified_at datetime,
                password varchar NOT NULL,
                remember_token varchar,
                created_at datetime,
                updated_at datetime,
                phone varchar,
                user_type varchar check (user_type in ('client', 'artisan', 'job_seeker')) not null default 'client',
                terms_accepted_at datetime,
                role varchar not null default 'user',
                status varchar not null default 'active',
                google_id varchar,
                facebook_id varchar,
                apple_id varchar,
                skills text,
                interests text,
                profile_photo varchar,
                bio text,
                province varchar,
                city varchar
            )");

            DB::statement("INSERT INTO users (
                id, name, email, email_verified_at, password, remember_token,
                created_at, updated_at, phone, user_type, terms_accepted_at,
                role, status, google_id, facebook_id, apple_id, skills, interests,
                profile_photo, bio, province, city
            )
            SELECT
                id, name, email, email_verified_at, password, remember_token,
                created_at, updated_at, phone, user_type, terms_accepted_at,
                role, status, google_id, facebook_id, apple_id, skills, interests,
                profile_photo, bio, province, city
            FROM users_reverted");

            DB::statement('UPDATE users SET user_type = "job_seeker" WHERE user_type = "recruiter"');
            DB::statement('DROP TABLE users_reverted');
            DB::statement('PRAGMA foreign_keys = ON');

            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('client', 'artisan', 'job_seeker') DEFAULT 'client'");
    }
};
