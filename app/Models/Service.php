<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'artisan_id',
        'category_id',
        'title',
        'description',
        'price',
        'location',
        'images',
        'is_verified',
        'status',
        'rating',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'rating' => 'decimal:2',
        'images' => 'array',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Get the artisan who created this service.
     */
    public function artisan()
    {
        return $this->belongsTo(User::class, 'artisan_id');
    }

    /**
     * Get the category of this service.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all service requests for this service.
     */
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    /**
     * Get all missions related to this service.
     */
    public function missions()
    {
        return $this->hasMany(Mission::class);
    }

    // ==========================================
    // Scopes
    // ==========================================

    /**
     * Filter active services.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Filter verified services.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Filter by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Filter by location.
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', "%{$location}%");
    }

    /**
     * Search services by title or description.
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('title', 'like', "%{$term}%")
                     ->orWhere('description', 'like', "%{$term}%");
    }
}

