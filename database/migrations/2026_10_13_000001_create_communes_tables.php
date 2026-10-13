<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Référentiel des communes de Kinshasa + voisinage, base du matching
 * (proximité par commune plutôt qu'en km, à cause des embouteillages).
 */
return new class extends Migration
{
    /**
     * Communes voisines (frontière commune). Saisie dans un seul sens,
     * la symétrie est assurée à l'insertion. Ajustable ensuite depuis l'admin.
     */
    private const NEIGHBORS = [
        'Gombe'        => ['Barumbu', 'Lingwala', 'Kintambo', 'Kinshasa'],
        'Barumbu'      => ['Kinshasa', 'Limete'],
        'Kinshasa'     => ['Lingwala', 'Kasa-Vubu', 'Kalamu', 'Limete'],
        'Lingwala'     => ['Kasa-Vubu', 'Kintambo', 'Bandalungwa'],
        'Kintambo'     => ['Ngaliema', 'Bandalungwa'],
        'Bandalungwa'  => ['Ngaliema', 'Selembao', 'Bumbu', 'Kasa-Vubu'],
        'Kasa-Vubu'    => ['Bumbu', 'Ngiri-Ngiri', 'Kalamu'],
        'Kalamu'       => ['Ngiri-Ngiri', 'Makala', 'Lemba', 'Limete'],
        'Ngiri-Ngiri'  => ['Bumbu', 'Makala'],
        'Bumbu'        => ['Makala', 'Selembao'],
        'Makala'       => ['Lemba', 'Ngaba', 'Selembao', 'Mont-Ngafula'],
        'Selembao'     => ['Ngaliema', 'Mont-Ngafula'],
        'Ngaliema'     => ['Mont-Ngafula'],
        'Mont-Ngafula' => ['Ngaba', 'Lemba', 'Kisenso', 'Kimbanseke'],
        'Lemba'        => ['Ngaba', 'Limete', 'Matete', 'Kisenso'],
        'Limete'       => ['Matete', 'Masina'],
        'Matete'       => ['Kisenso', 'Ndjili'],
        'Kisenso'      => ['Ndjili', 'Kimbanseke'],
        'Ndjili'       => ['Masina', 'Kimbanseke'],
        'Masina'       => ['Kimbanseke', 'Nsele'],
        'Kimbanseke'   => ['Nsele'],
        'Nsele'        => ['Maluku'],
        'Ngaba'        => [],
        'Maluku'       => [],
    ];

    public function up(): void
    {
        Schema::create('communes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('city')->default('Kinshasa');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('commune_neighbors', function (Blueprint $table) {
            $table->foreignId('commune_id')->constrained('communes')->cascadeOnDelete();
            $table->foreignId('neighbor_id')->constrained('communes')->cascadeOnDelete();
            $table->primary(['commune_id', 'neighbor_id']);
        });

        Schema::create('artisan_communes', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('commune_id')->constrained('communes')->cascadeOnDelete();
            $table->primary(['user_id', 'commune_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'commune_id')) {
                $column = $table->foreignId('commune_id')->nullable()->after('city');
                // En SQLite (tests), une contrainte ajoutée reconstruit la table users
                // et supprime les triggers de synchronisation users_old (migration
                // 2026_08_21_000001). La contrainte n'est donc posée qu'en MySQL.
                if (DB::getDriverName() !== 'sqlite') {
                    $column->constrained('communes')->nullOnDelete();
                }
            }
            if (!Schema::hasColumn('users', 'quartier')) {
                $table->string('quartier', 120)->nullable()->after('commune_id');
            }
        });

        $now = now();
        $ids = [];
        foreach (array_keys(self::NEIGHBORS) as $name) {
            $ids[$name] = DB::table('communes')->insertGetId([
                'name' => $name, 'slug' => Str::slug($name), 'city' => 'Kinshasa',
                'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        $pairs = [];
        foreach (self::NEIGHBORS as $name => $neighbors) {
            foreach ($neighbors as $neighbor) {
                $pairs[$ids[$name] . '-' . $ids[$neighbor]] = ['commune_id' => $ids[$name], 'neighbor_id' => $ids[$neighbor]];
                $pairs[$ids[$neighbor] . '-' . $ids[$name]] = ['commune_id' => $ids[$neighbor], 'neighbor_id' => $ids[$name]];
            }
        }
        DB::table('commune_neighbors')->insert(array_values($pairs));

        // Reprise des zones saisies en texte libre ("Gombe, Limete, ...").
        $bySlug = collect($ids)->mapWithKeys(fn ($id, $name) => [Str::slug($name) => $id]);
        DB::table('users')->whereNotNull('intervention_zone')->select('id', 'intervention_zone')->orderBy('id')
            ->each(function ($user) use ($bySlug) {
                $rows = collect(preg_split('/[,;\/]+/', $user->intervention_zone))
                    ->map(fn ($part) => $bySlug[Str::slug(trim($part))] ?? null)
                    ->filter()->unique()
                    ->map(fn ($communeId) => ['user_id' => $user->id, 'commune_id' => $communeId]);
                if ($rows->isNotEmpty()) {
                    DB::table('artisan_communes')->insertOrIgnore($rows->values()->all());
                }
            });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'commune_id')) {
                if (DB::getDriverName() !== 'sqlite') {
                    $table->dropForeign(['commune_id']);
                }
                $table->dropColumn('commune_id');
            }
            if (Schema::hasColumn('users', 'quartier')) {
                $table->dropColumn('quartier');
            }
        });
        Schema::dropIfExists('artisan_communes');
        Schema::dropIfExists('commune_neighbors');
        Schema::dropIfExists('communes');
    }
};
