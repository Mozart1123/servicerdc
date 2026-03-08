<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('service_requests', 'phone')) {
                $table->string('phone')->nullable()->after('user_id');
            }
            
            if (!Schema::hasColumn('service_requests', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('service_requests', 'category_needed')) {
                $table->string('category_needed')->nullable()->after('requested_service_name');
            }
            
            if (!Schema::hasColumn('service_requests', 'budget_min')) {
                $table->decimal('budget_min', 10, 2)->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('service_requests', 'budget_max')) {
                $table->decimal('budget_max', 10, 2)->nullable()->after('budget_min');
            }
            
            if (!Schema::hasColumn('service_requests', 'urgency')) {
                $table->enum('urgency', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('budget_max');
            }
            
            if (!Schema::hasColumn('service_requests', 'response')) {
                $table->text('response')->nullable()->after('notes');
            }
            
            if (!Schema::hasColumn('service_requests', 'responded_by')) {
                $table->foreignId('responded_by')->nullable()->constrained('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('service_requests', 'responded_at')) {
                $table->timestamp('responded_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'email',
                'category_needed',
                'budget_min',
                'budget_max',
                'urgency',
                'response',
                'responded_by',
                'responded_at',
            ]);
        });
    }
};
