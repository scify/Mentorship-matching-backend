<?php

namespace App\Console\Commands;

use App\StorageLayer\CvFileStorage;
use Illuminate\Console\Command;

class MoveCvFilesToPrivate extends Command {
    /**
     * @var string
     */
    protected $signature = 'cv:move-to-private';

    /**
     * @var string
     */
    protected $description = 'Moves the CVs uploaded before 2026-10 from the public disk to private storage. Safe to run again.';

    public function handle(CvFileStorage $cvFileStorage): int {
        $moved = $cvFileStorage->moveLegacyFilesToPrivate();
        $this->info("Moved {$moved} CV file(s) to private storage.");

        return self::SUCCESS;
    }
}
