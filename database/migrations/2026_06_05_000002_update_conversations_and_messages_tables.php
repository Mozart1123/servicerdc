<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update conversations table columns to match spec
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'user_one_id')) {
                $table->renameColumn('user_one', 'user_one_id');
            }
            if (!Schema::hasColumn('conversations', 'user_two_id')) {
                $table->renameColumn('user_two', 'user_two_id');
            }
            if (!Schema::hasColumn('conversations', 'related_type')) {
                $table->string('related_type')->nullable()->after('user_two_id');
            }
            if (!Schema::hasColumn('conversations', 'related_id')) {
                $table->unsignedBigInteger('related_id')->nullable()->after('related_type');
            }
        });

        // Update messages table to add receiver_id, message, attachment
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'receiver_id')) {
                $table->foreignId('receiver_id')->nullable()->constrained('users')->nullOnDelete()->after('sender_id');
            }
            if (!Schema::hasColumn('messages', 'message')) {
                $table->text('message')->nullable()->after('receiver_id');
            }
            if (!Schema::hasColumn('messages', 'attachment')) {
                $table->string('attachment')->nullable()->after('message');
            }
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn(['related_type', 'related_id']);
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['receiver_id', 'message', 'attachment']);
        });
    }
};
