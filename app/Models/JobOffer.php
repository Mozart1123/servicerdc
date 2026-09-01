<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOffer extends Model
{
    use HasFactory;

    protected $table = 'job_offers';

    protected $fillable = [
        'user_id',
        'employer_id',
        'title',
        'company_name',
        'company_logo',
        'cover_image',
        'location',
        'category',
        'job_category_id',
        'contract_type',
        'salary_range',
        'description',
        'requirements',
        'status',
        'start_date',
        'deadline',
        'is_urgent',
    ];

    protected $casts = [
        'start_date' => 'date',
        'deadline'   => 'date',
        'is_urgent'  => 'boolean',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Get the user who created this job offer.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the employer who created this job offer.
     */
    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    /**
     * Get all applications for this job.
     */
    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get the structured job category this offer belongs to.
     *
     * Named `jobCategory` (not `category`) so it doesn't collide with the
     * legacy `category` text column, which stays in place and in sync for
     * backward compatibility with existing views/search/filters.
     */
    public function jobCategory()
    {
        return $this->belongsTo(JobCategory::class);
    }

    // ==========================================
    // Accessors
    // ==========================================

    /**
     * Whether the application deadline has passed. Purely a display/logic
     * indicator — an expired offer is never modified or deleted by this;
     * only the recruiter can remove it, or push the deadline back.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->deadline !== null && $this->deadline->isPast();
    }

    // ==========================================
    // Scopes
    // ==========================================

    /**
     * Filter active job offers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Filter by contract type.
     */
    public function scopeByContractType($query, $type)
    {
        return $query->where('contract_type', $type);
    }

    /**
     * Filter by location.
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', "%{$location}%");
    }

    /**
     * Filter by category/sector.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Search job offers by title or description.
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('title', 'like', "%{$term}%")
                     ->orWhere('description', 'like', "%{$term}%")
                     ->orWhere('company_name', 'like', "%{$term}%");
    }

    /**
     * Filter non-expired job offers.
     */
    public function scopeNotExpired($query)
    {
        return $query->where('deadline', '>=', now())
                     ->orWhereNull('deadline');
    }
}

