<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareerRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'career_path_id',
        'match_score',
        'analysis',
    ];

    /**
     * Get the user that owns the recommendation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the career path for this recommendation.
     */
    public function careerPath()
    {
        return $this->belongsTo(CareerPath::class);
    }
}
