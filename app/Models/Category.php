<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'description'];

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Get all services in this category.
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    // ==========================================
    // Scopes
    // ==========================================

    /**
     * Find category by slug.
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }
}
