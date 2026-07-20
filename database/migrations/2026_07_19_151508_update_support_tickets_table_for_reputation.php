<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->foreignId('mission_id')->nullable()->after('user_id')
                  ->constrained('missions')->nullOnDelete();
            $table->enum('ticket_type', ['general', 'dispute'])->default('general')->after('subject');
            $table->enum('resolution', ['refund', 'no_refund', 'pending'])->nullable()->after('status');
            $table->decimal('refund_amount', 10, 2)->nullable()->after('resolution');
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropForeign(['mission_id']);
            $table->dropColumn(['mission_id', 'ticket_type', 'resolution', 'refund_amount']);
        });
    }
};
