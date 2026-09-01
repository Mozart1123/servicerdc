<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class JobCategory extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'image', 'description'];

    protected $appends = ['image_url'];

    // ==========================================
    // Accessors
    // ==========================================

    /**
     * Get the public URL of the category image (or null if none).
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return Storage::disk('public')->url($this->image);
        }
        return null;
    }

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Get all job offers in this category.
     */
    public function jobOffers()
    {
        return $this->hasMany(JobOffer::class);
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
