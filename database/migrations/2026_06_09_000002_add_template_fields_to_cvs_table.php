<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cvs', function (Blueprint $table) {
            $table->text('template_answers')->nullable()->after('portfolio_link');
            $table->text('summary')->nullable()->after('template_answers');
            $table->string('job_title')->nullable()->after('summary');
        });
    }

    public function down(): void
    {
        Schema::table('cvs', function (Blueprint $table) {
            $table->dropColumn(['template_answers', 'summary', 'job_title']);
        });
    }
};
