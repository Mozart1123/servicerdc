<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Commune extends Model
{
    protected $fillable = ['name', 'slug', 'city', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function neighbors(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'commune_neighbors', 'commune_id', 'neighbor_id');
    }

    /** Artisans qui acceptent d'intervenir dans cette commune. */
    public function artisans(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'artisan_communes', 'commune_id', 'user_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('name');
    }

    public function isNeighborOf(int $communeId): bool
    {
        return $this->neighbors->contains('id', $communeId);
    }
}
