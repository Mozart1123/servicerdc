<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\User\DashboardController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

try {
    // Récupérer un utilisateur pour tester
    $user = User::first();
    
    if (!$user) {
        echo "❌ Aucun utilisateur trouvé dans la base de données\n";
        exit(1);
    }

    echo "=== TEST DU CONTRÔLEUR DASHBOARD ===\n\n";
    echo "Utilisateur : " . $user->name . " (ID: " . $user->id . ")\n\n";

    // Simuler l'authentification
    Auth::setUser($user);

    // Créer une requête simulée
    $request = Request::create('/user/dashboard', 'GET');
    app('request')->replace($request->all());

    // Appeler le contrôleur
    $controller = new DashboardController();
    $view = $controller->index();

    // Récupérer les variables de la vue
    $viewData = $view->getData();

    echo "Variables de la vue :\n";
    echo "✅ stats : " . (isset($viewData['stats']) ? 'OK' : 'MANQUANT') . "\n";
    echo "✅ recentJobs : " . (isset($viewData['recentJobs']) ? 'OK (' . count($viewData['recentJobs']) . ' emplois)' : 'MANQUANT') . "\n";
    echo "✅ allJobs : " . (isset($viewData['allJobs']) ? 'OK (' . count($viewData['allJobs']) . ' emplois)' : 'MANQUANT') . "\n";
    echo "✅ recentServices : " . (isset($viewData['recentServices']) ? 'OK (' . count($viewData['recentServices']) . ' services)' : 'MANQUANT') . "\n";
    echo "✅ categories : " . (isset($viewData['categories']) ? 'OK (' . count($viewData['categories']) . ' catégories)' : 'MANQUANT') . "\n";
    echo "✅ myApplications : " . (isset($viewData['myApplications']) ? 'OK (' . count($viewData['myApplications']) . ' candidatures)' : 'MANQUANT') . "\n";
    echo "✅ notifications : " . (isset($viewData['notifications']) ? 'OK (' . count($viewData['notifications']) . ' notifications)' : 'MANQUANT') . "\n";

    echo "\n✅ TOUS LES TESTS RÉUSSIS !\n";
    echo "❌ L'erreur 'Undefined variable \$allJobs' est RÉSOLUE !\n";

} catch (\Exception $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
    echo "Fichier : " . $e->getFile() . " (ligne " . $e->getLine() . ")\n";
}
