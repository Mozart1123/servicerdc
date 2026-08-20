<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
            DB::statement('ALTER TABLE identity_verifications RENAME TO identity_verifications_old');

            DB::statement("CREATE TABLE identity_verifications (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                user_id INTEGER NOT NULL,
                identity_document_type varchar,
                identity_document_path varchar,
                selfie_path varchar,
                verification_status varchar NOT NULL DEFAULT 'not_submitted',
                verification_rejection_reason varchar,
                verification_rejection_comment text,
                verified_at datetime,
                verified_by INTEGER,
                created_at datetime,
                updated_at datetime,
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY(verified_by) REFERENCES users(id)
            )");

            DB::statement("INSERT INTO identity_verifications (
                id, user_id, identity_document_type, identity_document_path, selfie_path,
                verification_status, verification_rejection_reason, verification_rejection_comment,
                verified_at, verified_by, created_at, updated_at
            )
            SELECT
                id, user_id, identity_document_type, identity_document_path, selfie_path,
                verification_status, verification_rejection_reason, verification_rejection_comment,
                verified_at, verified_by, created_at, updated_at
            FROM identity_verifications_old");

            DB::statement('DROP TABLE identity_verifications_old');
            DB::statement('PRAGMA foreign_keys = ON');

            return;
        }

        Schema::table('identity_verifications', function (Blueprint $table) {
            $table->string('identity_document_type')->nullable()->change();
            $table->string('identity_document_path')->nullable()->change();
            $table->string('selfie_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
            DB::statement('ALTER TABLE identity_verifications RENAME TO identity_verifications_old');

            DB::statement("CREATE TABLE identity_verifications (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                user_id INTEGER NOT NULL,
                identity_document_type varchar NOT NULL,
                identity_document_path varchar NOT NULL,
                selfie_path varchar NOT NULL,
                verification_status varchar NOT NULL DEFAULT 'not_submitted',
                verification_rejection_reason varchar,
                verification_rejection_comment text,
                verified_at datetime,
                verified_by INTEGER,
                created_at datetime,
                updated_at datetime,
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY(verified_by) REFERENCES users(id)
            )");

            DB::statement("INSERT INTO identity_verifications (
                id, user_id, identity_document_type, identity_document_path, selfie_path,
                verification_status, verification_rejection_reason, verification_rejection_comment,
                verified_at, verified_by, created_at, updated_at
            )
            SELECT
                id, user_id, identity_document_type, identity_document_path, selfie_path,
                verification_status, verification_rejection_reason, verification_rejection_comment,
                verified_at, verified_by, created_at, updated_at
            FROM identity_verifications_old");

            DB::statement('DROP TABLE identity_verifications_old');
            DB::statement('PRAGMA foreign_keys = ON');

            return;
        }

        Schema::table('identity_verifications', function (Blueprint $table) {
            $table->string('identity_document_type')->nullable(false)->change();
            $table->string('identity_document_path')->nullable(false)->change();
            $table->string('selfie_path')->nullable(false)->change();
        });
    }
};
