<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    /**
     * The 9 sector options that used to be hard-coded in the job-offer forms.
     * Kept here (not on the model) since this list only matters for this
     * one-time backfill.
     */
    private const HISTORICAL_CATEGORIES = [
        'Informatique' => 'Informatique & Technologie',
        'Santé'        => 'Santé & Médical',
        'Éducation'    => 'Éducation',
        'Finance'      => 'Finance & Comptabilité',
        'Commerce'     => 'Vente & Commerce',
        'BTP'          => 'BTP & Construction',
        'Transport'    => 'Transport & Logistique',
        'Hôtellerie'   => 'Hôtellerie & Restauration',
        'Autre'        => 'Autre secteur',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('job_offers', 'job_category_id')) {
            Schema::table('job_offers', function (Blueprint $table) {
                $table->foreignId('job_category_id')
                    ->nullable()
                    ->after('category')
                    ->constrained('job_categories')
                    ->nullOnDelete();
            });
        }

        $now = now();
        $usedSlugs = DB::table('job_categories')->pluck('slug')->flip()->toArray();
        $categoryIdByName = [];

        // 1) Create the 9 historical categories (if not already present by name).
        foreach (self::HISTORICAL_CATEGORIES as $name => $description) {
            $existing = DB::table('job_categories')->where('name', $name)->first();
            if ($existing) {
                $categoryIdByName[$name] = $existing->id;
                continue;
            }

            $slug = $this->uniqueSlug($name, $usedSlugs);
            $usedSlugs[$slug] = true;

            $categoryIdByName[$name] = DB::table('job_categories')->insertGetId([
                'name'        => $name,
                'slug'        => $slug,
                'description' => $description,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        // 2) Create a category for any other pre-existing `category` text value
        //    (e.g. manually edited data) that isn't one of the 9 above — nothing lost.
        $otherValues = DB::table('job_offers')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category');

        foreach ($otherValues as $name) {
            if (isset($categoryIdByName[$name])) {
                continue;
            }

            $slug = $this->uniqueSlug($name, $usedSlugs);
            $usedSlugs[$slug] = true;

            $categoryIdByName[$name] = DB::table('job_categories')->insertGetId([
                'name'        => $name,
                'slug'        => $slug,
                'description' => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        // 3) Link every existing job offer to the right category via its old text field.
        foreach ($categoryIdByName as $name => $id) {
            DB::table('job_offers')->where('category', $name)->update(['job_category_id' => $id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('job_offers', 'job_category_id')) {
            Schema::table('job_offers', function (Blueprint $table) {
                $table->dropForeign(['job_category_id']);
                $table->dropColumn('job_category_id');
            });
        }
    }

    /**
     * Generate a unique slug against a pre-loaded cache of already-used slugs
     * (avoids an extra query per row and matches the pattern used elsewhere
     * for category slug generation).
     *
     * @param array<string, mixed> $usedSlugs Passed by reference, accumulates as slugs are claimed.
     */
    private function uniqueSlug(string $name, array &$usedSlugs): string
    {
        $slug = Str::slug($name);
        $base = $slug;
        $i = 1;
        while (array_key_exists($slug, $usedSlugs)) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
};
