<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Un artisan qu'un client a ajouté à ses favoris depuis le profil public
 * de l'artisan (bouton cœur).
 */
class ArtisanFavorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'artisan_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function artisan()
    {
        return $this->belongsTo(User::class, 'artisan_id');
    }
}
