<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Traits\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ApiKeyController extends Controller
{
    use AuditLogger;

    /**
     * Display a listing of API keys.
     */
    public function index(): View
    {
        $keys = ApiKey::with('user')->latest()->get();
        return view('super-admin.api-keys.index', compact('keys'));
    }

    /**
     * Store a newly created API key.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $plainToken = ApiKey::generateToken();

        $apiKey = ApiKey::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'key' => hash('sha256', $plainToken),
            'token_last_four' => substr($plainToken, -4),
            'expires_at' => $request->expires_at,
            'status' => 'active',
        ]);

        $this->auditLog('created', "Generated new API Key: {$apiKey->name}", $apiKey, [
            'expires_at' => $apiKey->expires_at
        ], 'warning');

        return redirect()->route('super-admin.system.api-keys.index')
            ->with('success', 'API Key generated successfully.')
            ->with('plain_token', $plainToken);
    }

    /**
     * Revoke the specified API key.
     */
    public function revoke(ApiKey $apiKey)
    {
        $apiKey->update(['status' => 'revoked']);

        $this->auditLog('revoked', "Revoked API Key: {$apiKey->name}", $apiKey, [], 'danger');

        return redirect()->route('super-admin.system.api-keys.index')
            ->with('success', "API Key \"{$apiKey->name}\" has been revoked.");
    }
}
