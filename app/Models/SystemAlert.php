<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SystemAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'description',
        'level',
        'is_resolved',
        'resolved_by',
        'data',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'data' => 'array',
    ];

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
