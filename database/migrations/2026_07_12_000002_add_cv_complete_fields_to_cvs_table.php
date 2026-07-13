<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cvs', function (Blueprint $table) {
            if (!Schema::hasColumn('cvs', 'province')) {
                $table->string('province')->nullable()->after('address');
            }
            if (!Schema::hasColumn('cvs', 'experience')) {
                $table->text('experience')->nullable()->change();
            }
            if (!Schema::hasColumn('cvs', 'education')) {
                $table->text('education')->nullable()->change();
            }
            if (!Schema::hasColumn('cvs', 'skills')) {
                $table->text('skills')->nullable()->change();
            }
            if (!Schema::hasColumn('cvs', 'languages')) {
                $table->text('languages')->nullable()->change();
            }
            if (!Schema::hasColumn('cvs', 'cv_file')) {
                $table->string('cv_file')->nullable()->after('cv_file_path');
            }
            if (!Schema::hasColumn('cvs', 'diploma_file')) {
                $table->string('diploma_file')->nullable()->after('cv_file');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cvs', function (Blueprint $table) {
            if (Schema::hasColumn('cvs', 'province')) {
                $table->dropColumn('province');
            }
            if (Schema::hasColumn('cvs', 'cv_file')) {
                $table->dropColumn('cv_file');
            }
            if (Schema::hasColumn('cvs', 'diploma_file')) {
                $table->dropColumn('diploma_file');
            }
        });
    }
};
