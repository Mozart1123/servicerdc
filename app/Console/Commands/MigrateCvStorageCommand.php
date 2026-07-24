<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MigrateCvStorageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cv:migrate-storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate CV and diploma files from public disk to private local disk and delete public originals.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $directories = ['cv_files', 'cv_diplomas', 'job_applications/cvs', 'job_applications'];

        $countMoved = 0;

        foreach ($directories as $dir) {
            $publicPath = storage_path('app/public/' . $dir);
            $localPath  = storage_path('app/' . $dir);

            if (!File::exists($publicPath)) {
                continue;
            }

            if (!File::exists($localPath)) {
                File::makeDirectory($localPath, 0755, true);
            }

            $files = File::allFiles($publicPath);

            foreach ($files as $file) {
                $relativePath = $file->getRelativePathname();
                $targetFile = $localPath . '/' . $relativePath;
                $targetDir  = dirname($targetFile);

                if (!File::exists($targetDir)) {
                    File::makeDirectory($targetDir, 0755, true);
                }

                // Copy file to private local storage
                File::copy($file->getRealPath(), $targetFile);
                
                // Delete original public file so it is no longer accessible via /storage/...
                File::delete($file->getRealPath());

                $countMoved++;
                $this->info("Déplacé : {$dir}/{$relativePath} vers le stockage privé (original supprimé du public)");
            }
        }

        $this->info("Migration terminée avec succès ! Total de fichiers déplacés : {$countMoved}");

        return Command::SUCCESS;
    }
}
