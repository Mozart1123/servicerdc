<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareerPath extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'required_skills',
        'interests_match',
        'salary_range',
        'industry',
    ];

    protected $casts = [
        'required_skills' => 'array',
        'interests_match' => 'array',
    ];

    /**
     * Get recommendations for this career path.
     */
    public function recommendations()
    {
        return $this->hasMany(CareerRecommendation::class);
    }
}
