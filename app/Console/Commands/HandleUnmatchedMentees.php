<?php

namespace App\Console\Commands;

use App\Notifications\MenteeStillUnmatched;
use App\StorageLayer\MenteeStorage;
use Illuminate\Console\Command;

class HandleUnmatchedMentees extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mentees:handle-unmatched';

    private $menteeStorage;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Selects the mentees who have not been matched yet (for more than 7 months), and sends them a relevant email.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MenteeStorage $menteeStorage) {
        parent::__construct();
        $this->menteeStorage = $menteeStorage;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $mentees = $this->menteeStorage->getUnmatchedMenteesCreatedSince(7);
        foreach ($mentees as $mentee) {
            $mentee->notify(new MenteeStillUnmatched());
        }
        return 0;
    }
}
