<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
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

        $throttleKey = Str::transliterate(Str::lower($credentials['email']) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'email' => "Trop de tentatives de connexion. Veuillez réessayer dans {$seconds} secondes.",
            ])->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);

            $user = Auth::user();

            if ($user->status === User::STATUS_SUSPENDED) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Votre compte a été suspendu. Veuillez contacter le support.',
                ])->onlyInput('email');
            }

            // Recruitment feature toggle — existing recruiter accounts are
            // blocked at login (not modified/deleted) while the feature is
            // switched off. Checked directly against TYPE_RECRUITER (not
            // isRecruiter(), which also covers job_seeker in this codebase).
            if ($user->user_type === User::TYPE_RECRUITER && !config('features.recruitment_enabled')) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'La fonctionnalité de recrutement est temporairement suspendue. Nous vous informerons dès sa réactivation.',
                ])->onlyInput('email');
            }

            // Artisans/recruteurs restent en attente de validation admin tant
            // que leur compte n'a pas été approuvé depuis /admin/users-mgmt/pending
            // — sans ce contrôle, se reconnecter suffisait à contourner
            // entièrement la validation (celle-ci n'était vérifiée qu'à
            // l'inscription, jamais aux connexions suivantes). Les clients et
            // chercheurs d'emploi ne sont pas concernés (cohérent avec le
            // reste du flux : seuls artisan/recruteur déclenchent une
            // notification admin à l'inscription).
            if (
                $user->role === User::ROLE_USER
                && in_array($user->user_type, [User::TYPE_ARTISAN, User::TYPE_RECRUITER], true)
                && $user->status === User::STATUS_PENDING
            ) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Votre compte est en attente de validation par un administrateur.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return $this->redirectToDashboard($user);
        }

        RateLimiter::hit($throttleKey, 60);

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

        return redirect()->route('login')
            ->with('status', 'Vous avez été déconnecté avec succès.');
    }

    /**
     * Redirect user to their role-specific dashboard.
     * Uses PHP 8.2 match expression for clean, readable routing.
     */
    private function redirectToDashboard(User $user): RedirectResponse
    {
        if ($user->user_type === User::TYPE_CLIENT && $user->role === User::ROLE_USER) {
            return redirect()->intended(route('home'))
                ->with('success', 'Connexion réussie. Bienvenue sur ProConnect !');
        }

        $route = match ($user->role) {
            User::ROLE_SUPER_ADMIN => 'super-admin.dashboard',
            User::ROLE_ADMIN => 'admin.dashboard',
            User::ROLE_USER => 'user.dashboard',
            default => 'user.dashboard',
        };

        $message = match ($user->role) {
            User::ROLE_SUPER_ADMIN => 'Bienvenue Super Administrateur !',
            User::ROLE_ADMIN => 'Bienvenue Administrateur !',
            default => 'Connexion réussie. Bienvenue sur ProConnect !',
        };

        return redirect()->intended(route($route))
            ->with('success', $message);
    }
}
