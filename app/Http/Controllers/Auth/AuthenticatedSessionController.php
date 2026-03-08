<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Authenticated Session Controller
 * 
 * Handles user login/logout with role-based redirection.
 * Uses PHP 8.2 match syntax for clean redirection logic.
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'L\'adresse email est requise.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'password.required' => 'Le mot de passe est requis.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if ($user->status === User::STATUS_SUSPENDED) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Votre compte a été suspendu. Veuillez contacter le support.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return $this->redirectToDashboard($user);
        }

        return back()->withErrors([
            'email' => 'Ces identifiants ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('status', 'Vous avez été déconnecté avec succès.');
    }

    /**
     * Redirect user to their role-specific dashboard.
     * Uses PHP 8.2 match expression for clean, readable routing.
     */
    private function redirectToDashboard(User $user): RedirectResponse
    {
        $route = match ($user->role) {
            User::ROLE_SUPER_ADMIN => 'super-admin.dashboard',
            User::ROLE_ADMIN => 'admin.dashboard',
            User::ROLE_USER => 'user.dashboard',
            default => 'user.dashboard',
        };

        $message = match ($user->role) {
            User::ROLE_SUPER_ADMIN => 'Bienvenue Super Administrateur !',
            User::ROLE_ADMIN => 'Bienvenue Administrateur !',
            default => 'Connexion réussie. Bienvenue sur ServiceRDC !',
        };

        return redirect()->intended(route($route))
            ->with('success', $message);
    }
}
