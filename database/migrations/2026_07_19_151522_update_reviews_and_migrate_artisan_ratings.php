<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add audit traceability column to reviews
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('migrated_from_artisan_rating_id')
                  ->nullable()
                  ->after('rejection_reason')
                  ->comment('Temporary audit column: source ArtisanRating ID from data migration');
        });

        // 2. Migrate existing ArtisanRating data into reviews
        if (Schema::hasTable('artisan_ratings')) {
            $ratings = DB::table('artisan_ratings')->get();

            foreach ($ratings as $rating) {
                // Avoid duplicates if this migration is run multiple times
                $alreadyMigrated = DB::table('reviews')
                    ->where('migrated_from_artisan_rating_id', $rating->id)
                    ->exists();

                if ($alreadyMigrated) {
                    continue;
                }

                // Try to find an associated mission via the service_request relation
                $missionId = null;
                if (!empty($rating->service_request_id)) {
                    $mission = DB::table('missions')
                        ->where('artisan_id', $rating->artisan_id)
                        ->whereExists(function ($q) use ($rating) {
                            $q->select(DB::raw(1))
                              ->from('service_requests')
                              ->where('service_requests.id', $rating->service_request_id)
                              ->whereColumn('service_requests.service_id', 'missions.service_id');
                        })
                        ->first();
                    $missionId = $mission?->id;
                }

                DB::table('reviews')->insert([
                    'mission_id'                       => $missionId,
                    'client_id'                        => $rating->user_id,
                    'artisan_id'                       => $rating->artisan_id,
                    'rating'                           => $rating->rating,
                    'feedback'                         => $rating->comment ?? null,
                    'status'                           => 'approved', // was valid in old system
                    'rejection_reason'                 => null,
                    'migrated_from_artisan_rating_id'  => $rating->id,
                    'created_at'                       => $rating->created_at,
                    'updated_at'                       => $rating->updated_at,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Remove migrated records (only those with a trace ID)
        DB::table('reviews')->whereNotNull('migrated_from_artisan_rating_id')->delete();

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('migrated_from_artisan_rating_id');
        });
    }
};
