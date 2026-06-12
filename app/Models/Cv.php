<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Cv extends Model
{
    use HasFactory;

    protected $table = 'cvs';

    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone_number',
        'address',
        'education',
        'skills',
        'experience',
        'languages',
        'cv_file_path',
        'portfolio_link',
        'profile_photo',
        'cv_file',
        'template_answers',
        'summary',
        'job_title',
    ];

    protected $casts = [
        'education'        => 'array',
        'skills'           => 'array',
        'experience'       => 'array',
        'languages'        => 'array',
        'template_answers' => 'array',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getSkillsListAttribute(): array
    {
        $raw = $this->skills;
        if (is_array($raw)) return $raw;
        if (is_string($raw)) return array_map('trim', explode(',', $raw));
        return [];
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->profile_photo) {
            return Storage::url($this->profile_photo);
        }
        if ($this->user?->profile_photo) {
            return Storage::url($this->user->profile_photo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name ?? 'User') . '&background=29B6D1&color=fff&size=128';
    }

    public function getCvFileUrlAttribute(): ?string
    {
        $path = $this->cv_file ?? $this->cv_file_path;
        return $path ? Storage::url($path) : null;
    }
}
