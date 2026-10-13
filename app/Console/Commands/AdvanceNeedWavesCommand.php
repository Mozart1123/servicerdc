<?php

namespace App\Console\Commands;

use App\Services\Matching\NeedDispatcher;
use Illuminate\Console\Command;

/** Envoie la vague suivante des demandes publiées dont le délai de réponse est écoulé. */
class AdvanceNeedWavesCommand extends Command
{
    protected $signature = 'proconnect:advance-need-waves';

    protected $description = 'Fait avancer les vagues d\'envoi des demandes publiées (matching)';

    public function handle(NeedDispatcher $dispatcher): int
    {
        $count = $dispatcher->advanceDue();
        $this->info("{$count} demande(s) examinée(s).");

        return self::SUCCESS;
    }
}
