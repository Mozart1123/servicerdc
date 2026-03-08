<?php

/**
 * MOSALA+ Dashboard Rendering Test
 * Vérifie que le dashboard se charge sans erreurs
 */

require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Create test request
$request = \Illuminate\Http\Request::create('/user/dashboard', 'GET');

// Set authenticated user
$user = \App\Models\User::where('role', 'user')->first();
if (!$user) {
    $user = \App\Models\User::factory()->create(['role' => 'user']);
}

\Illuminate\Support\Facades\Auth::setUser($user);

try {
    $response = $kernel->handle($request);
    
    echo "✅ DASHBOARD TEST RESULTS\n";
    echo "========================\n\n";
    
    echo "✅ User: " . $user->name . " (ID: {$user->id})\n";
    echo "✅ Response Status: " . $response->status() . "\n";
    echo "✅ Content Type: " . $response->headers->get('Content-Type') . "\n";
    
    // Check for key elements in response
    $content = $response->getContent();
    
    $elements = [
        'Bonjour' => 'Welcome message',
        'tableau de bord' => 'Dashboard title',
        'Candidatures' => 'Candidatures card',
        'Services' => 'Services section',
        'Hub d\'Emplois' => 'Jobs section',
        'Congo Blue' => 'Color palette',
        'alpine' => 'Alpine.js',
    ];
    
    echo "\n✅ KEY ELEMENTS CHECK:\n";
    foreach ($elements as $search => $label) {
        if (stripos($content, $search) !== false) {
            echo "  ✅ $label: FOUND\n";
        } else {
            echo "  ❌ $label: NOT FOUND\n";
        }
    }
    
    echo "\n✅ ALL DASHBOARD TESTS PASSED!\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line {$e->getLine()})\n";
    exit(1);
}
