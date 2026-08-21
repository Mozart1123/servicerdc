<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            return;
        }

        if (!Schema::hasTable('users')) {
            return;
        }

        $tablesToRepair = ['users_old', 'users_old_backup'];

        foreach ($tablesToRepair as $tableName) {
            if (Schema::hasTable($tableName)) {
                DB::statement('DROP TABLE ' . $tableName);
            }
        }

        foreach ($tablesToRepair as $tableName) {
            DB::statement("CREATE TABLE {$tableName} (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                name varchar NOT NULL,
                email varchar NOT NULL,
                email_verified_at datetime,
                password varchar NOT NULL,
                remember_token varchar,
                created_at datetime,
                updated_at datetime,
                phone varchar,
                user_type varchar NOT NULL DEFAULT 'client',
                terms_accepted_at datetime,
                role varchar NOT NULL DEFAULT 'user',
                status varchar NOT NULL DEFAULT 'active',
                google_id varchar,
                facebook_id varchar,
                apple_id varchar,
                skills text,
                interests text,
                profile_photo varchar,
                bio text,
                province varchar,
                city varchar,
                company_description text
            )");

            DB::statement("INSERT INTO {$tableName} (
                    id, name, email, email_verified_at, password, remember_token,
                    created_at, updated_at, phone, user_type, terms_accepted_at,
                    role, status, google_id, facebook_id, apple_id, skills, interests,
                    profile_photo, bio, province, city, company_description
                )
                SELECT
                    id, name, email, email_verified_at, password, remember_token,
                    created_at, updated_at, phone, user_type, terms_accepted_at,
                    role, status, google_id, facebook_id, apple_id, skills, interests,
                    profile_photo, bio, province, city, company_description
                FROM users");
        }

        $columns = Schema::getColumnListing('users');
        $insertColumns = implode(', ', array_map(fn ($column) => '"' . $column . '"', $columns));
        $insertValues = implode(', ', array_map(fn ($column) => 'NEW."' . $column . '"', $columns));
        $updateAssignments = implode(', ', array_map(fn ($column) => '"' . $column . '" = NEW."' . $column . '"', $columns));

        foreach (['users_old', 'users_old_backup'] as $targetTable) {
            DB::statement('DROP TRIGGER IF EXISTS ' . $targetTable . '_sync_after_insert');
            DB::statement(
                'CREATE TRIGGER ' . $targetTable . '_sync_after_insert AFTER INSERT ON users BEGIN ' .
                'INSERT OR REPLACE INTO ' . $targetTable . ' (' . $insertColumns . ') VALUES (' . $insertValues . '); END;'
            );

            DB::statement('DROP TRIGGER IF EXISTS ' . $targetTable . '_sync_after_update');
            DB::statement(
                'CREATE TRIGGER ' . $targetTable . '_sync_after_update AFTER UPDATE ON users BEGIN ' .
                'UPDATE ' . $targetTable . ' SET ' . $updateAssignments . ' WHERE id = OLD.id; END;'
            );

            DB::statement('DROP TRIGGER IF EXISTS ' . $targetTable . '_sync_after_delete');
            DB::statement(
                'CREATE TRIGGER ' . $targetTable . '_sync_after_delete AFTER DELETE ON users BEGIN ' .
                'DELETE FROM ' . $targetTable . ' WHERE id = OLD.id; END;'
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            return;
        }

        DB::statement('DROP TRIGGER IF EXISTS users_old_sync_after_insert');
        DB::statement('DROP TRIGGER IF EXISTS users_old_sync_after_update');
        DB::statement('DROP TRIGGER IF EXISTS users_old_sync_after_delete');

        if (Schema::hasTable('users_old')) {
            DB::statement('DROP TABLE users_old');
        }
    }
};
