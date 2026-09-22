<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    public const PRICING_TYPES = [
        'fixed'         => 'Prix fixe',
        'starting_from' => 'À partir de',
        'quote'         => 'Sur devis',
    ];

    public const DURATIONS = [
        '30_minutes'    => '30 minutes',
        '45_minutes'    => '45 minutes',
        '1_hour'        => '1 heure',
        '1h30'          => '1 h 30',
        '2_hours'       => '2 heures',
        '3_hours'       => '3 heures',
        'half_day'      => 'Demi-journée',
        'full_day'      => 'Journée complète',
        'multiple_days' => 'Plusieurs jours',
        'to_define'     => 'À définir avec le client',
    ];

    public const SERVICE_LOCATIONS = [
        'home'     => 'À domicile',
        'provider' => 'Chez moi',
        'both'     => 'Les deux',
    ];

    public const AVAILABILITIES = [
        'daily'              => 'Tous les jours',
        'weekdays'           => 'Du lundi au vendredi',
        'monday_saturday'    => 'Du lundi au samedi',
        'weekends'           => 'Week-ends uniquement',
        'appointment'        => 'Sur rendez-vous',
        'availability_based' => 'Selon disponibilité',
    ];

    public const MIN_NOTICES = [
        'none'     => 'Aucun',
        '2_hours'  => '2 heures',
        '6_hours'  => '6 heures',
        '12_hours' => '12 heures',
        '24_hours' => '24 heures',
        '48_hours' => '48 heures',
        '72_hours' => '72 heures',
    ];

    protected $fillable = [
        'artisan_id',
        'category_id',
        'service_type_id',
        'provider_name',
        'profession',
        'city',
        'phone_number',
        'title',
        'description',
        'price',
        'pricing_type',
        'duration',
        'service_location',
        'availability',
        'min_notice',
        'location',
        'service_image',
        'gallery_images',
        'is_verified',
        'status',
        'rating',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'rating' => 'decimal:2',
        'price' => 'decimal:2',
        'gallery_images' => 'array',
        'images' => 'array',
    ];

    /**
     * Libellé du type de tarification.
     */
    public function pricingTypeLabel(): string
    {
        return self::PRICING_TYPES[$this->pricing_type] ?? self::PRICING_TYPES['fixed'];
    }

    /**
     * Libellé de la durée approximative.
     */
    public function durationLabel(): ?string
    {
        return $this->duration ? (self::DURATIONS[$this->duration] ?? $this->duration) : null;
    }

    /**
     * Libellé du lieu de prestation.
     */
    public function serviceLocationLabel(): ?string
    {
        return $this->service_location ? (self::SERVICE_LOCATIONS[$this->service_location] ?? $this->service_location) : null;
    }

    /**
     * Libellé de la disponibilité.
     */
    public function availabilityLabel(): ?string
    {
        return $this->availability ? (self::AVAILABILITIES[$this->availability] ?? $this->availability) : null;
    }

    /**
     * Libellé du préavis minimum.
     */
    public function minNoticeLabel(): ?string
    {
        return $this->min_notice ? (self::MIN_NOTICES[$this->min_notice] ?? $this->min_notice) : null;
    }

    /**
     * Affichage formatté du prix prenant en compte "Sur devis" et "À partir de".
     */
    public function formattedPrice(): string
    {
        if ($this->pricing_type === 'quote') {
            return 'Sur devis';
        }

        $formatted = number_format((float) ($this->price ?? 0), 2);

        if ($this->pricing_type === 'starting_from') {
            return "À partir de {$formatted}$";
        }

        return "{$formatted}$";
    }

    /**
     * Accessor pour le prix formaté.
     */
    public function getFormattedPriceAttribute(): string
    {
        return $this->formattedPrice();
    }


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
     * Get the service type of this service.
     */
    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
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

