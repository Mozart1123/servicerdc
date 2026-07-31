<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
    ];

    /**
     * Get the category that owns the service type.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the services associated with this service type.
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'service_type_id');
    }
}
