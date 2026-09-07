<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Une photo de réalisation publiée par un artisan (vitrine de ses travaux
 * passés), affichée dans l'onglet "Réalisations" de son profil public.
 */
class ArtisanRealisation extends Model
{
    use HasFactory;

    protected $fillable = [
        'artisan_id',
        'image_path',
        'caption',
    ];

    public function artisan()
    {
        return $this->belongsTo(User::class, 'artisan_id');
    }

    public function getImageUrlAttribute(): string
    {
        return Storage::url($this->image_path);
    }
}
