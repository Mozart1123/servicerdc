<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the provider authentication page.
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from provider.
     */
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            $column = $provider . '_id';
            
            $user = User::where($column, $socialUser->id)
                        ->orWhere('email', $socialUser->email)
                        ->first();

            if ($user) {
                // Link the provider ID if it's missing (email matched)
                if (!$user->$column) {
                    $user->update([$column => $socialUser->id]);
                }
                
                Auth::login($user);
                return redirect()->intended('dashboard');
            } else {
                // Create a new user
                $newUser = User::create([
                    'name' => $socialUser->name ?? $socialUser->nickname,
                    'email' => $socialUser->email,
                    $column => $socialUser->id,
                    'password' => Hash::make(Str::random(24)),
                    'role' => 'user', // Default role
                    'email_verified_at' => now(),
                ]);

                Auth::login($newUser);
                return redirect()->intended('dashboard');
            }

        } catch (Exception $e) {
            return redirect()->route('login')->with('error', "Connexion via {$provider} échouée. Veuillez réessayer.");
        }
    }
}
