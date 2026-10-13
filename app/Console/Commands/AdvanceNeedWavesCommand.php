<?php

namespace App\Console\Commands;

use App\Services\Matching\NeedDispatcher;
use App\Services\Matching\NeedLifecycle;
use Illuminate\Console\Command;

/**
 * Cycle de vie des demandes publiées, chaque minute :
 * 1. ferme les demandes expirées (7 jours sans suite) ;
 * 2. prévient les clients sans proposition après 24 h ;
 * 3. envoie la vague suivante quand le délai de réponse est écoulé.
 */
class AdvanceNeedWavesCommand extends Command
{
    protected $signature = 'proconnect:advance-need-waves';

    protected $description = 'Expiration, relance et vagues d\'envoi des demandes publiées (matching)';

    public function handle(NeedLifecycle $lifecycle, NeedDispatcher $dispatcher): int
    {
        $expired = $lifecycle->expireDue();
        $stale = $lifecycle->notifyStale();
        $advanced = $dispatcher->advanceDue();

        $this->info("{$expired} expirée(s), {$stale} relance(s) client, {$advanced} demande(s) examinée(s) pour les vagues.");

        return self::SUCCESS;
    }
}
