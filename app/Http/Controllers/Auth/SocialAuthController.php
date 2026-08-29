<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
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
                // Link the provider ID if missing (email matched)
                if (!$user->$column) {
                    $user->update([$column => $socialUser->id]);
                }
                
                Auth::login($user);

                // If user doesn't have a user_type set, force selection
                if (empty($user->user_type)) {
                    return redirect()->route('auth.select-user-type');
                }

                return redirect()->intended('dashboard');
            } else {
                // Create a new user with user_type = null temporarily
                $newUser = User::create([
                    'name' => $socialUser->name ?? $socialUser->nickname,
                    'email' => $socialUser->email,
                    $column => $socialUser->id,
                    'password' => Hash::make(Str::random(24)),
                    'user_type' => null,
                    'email_verified_at' => now(),
                ]);

                // role is not mass-assignable; set explicitly via forceFill.
                $newUser->forceFill(['role' => User::ROLE_USER])->save();

                Auth::login($newUser);

                // Redirect to profile type selection screen
                return redirect()->route('auth.select-user-type');
            }

        } catch (Exception $e) {
            \Log::error('Erreur de connexion sociale: ' . $e->getMessage(), [
                'provider' => $provider,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')->with('error', "Connexion via {$provider} échouée. Veuillez réessayer.");
        }
    }

    /**
     * Show the profile type selection screen for social login users.
     */
    public function showSelectUserType()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!empty($user->user_type)) {
            return redirect()->route('dashboard');
        }

        return view('auth.select-user-type');
    }

    /**
     * Store the chosen user_type for social login users.
     */
    public function storeUserType(Request $request)
    {
        $request->validate([
            'user_type' => ['required', 'string', 'in:client,artisan,recruiter'],
        ], [
            'user_type.required' => 'Veuillez sélectionner un type de profil.',
            'user_type.in'       => 'Type de profil invalide.',
        ]);

        $user = Auth::user();

        $user->update([
            'user_type' => $request->user_type,
        ]);

        return redirect()->route('dashboard')->with('success', 'Votre profil a été configuré avec succès. Bienvenue sur ProConnect !');
    }
}
