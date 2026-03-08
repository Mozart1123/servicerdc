<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Service;
use Illuminate\Support\Facades\DB;

try {
    // Test 1 : Compter les services actifs
    echo "=== TEST 1: Services actifs ===\n";
    $count = Service::active()->count();
    echo "Nombre de services actifs : " . $count . "\n\n";

    // Test 2 : Vérifier la structure de la table
    echo "=== TEST 2: Structure de la table services ===\n";
    $columns = DB::select("DESCRIBE services");
    foreach ($columns as $column) {
        echo $column->Field . " (" . $column->Type . ")\n";
    }
    echo "\n";

    // Test 3 : Services vérifiés
    echo "=== TEST 3: Services vérifiés ===\n";
    $verified = Service::verified()->count();
    echo "Nombre de services vérifiés : " . $verified . "\n\n";

    // Test 4 : Services actifs ET vérifiés
    echo "=== TEST 4: Services actifs ET vérifiés ===\n";
    $both = Service::active()->verified()->count();
    echo "Nombre de services actifs ET vérifiés : " . $both . "\n\n";

    echo "✅ TOUS LES TESTS RÉUSSIS !\n";
    
} catch (\Exception $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
    echo "Fichier : " . $e->getFile() . " (ligne " . $e->getLine() . ")\n";
}
