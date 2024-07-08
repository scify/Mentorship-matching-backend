<?php

namespace App\Console\Commands;

use App\BusinessLogicLayer\enums\MenteeStatuses;
use App\Notifications\MenteeStillUnmatched;
use App\StorageLayer\MenteeStorage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class HandleUnmatchedMentees extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mentees:notify-unmatched';

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
        $mentees = $this->menteeStorage->getUnmatchedMenteesCreatedSince(6);
        $num_of_mentees_rejected = 0;
        $num_of_mentees_notified = 0;
        foreach ($mentees as $mentee) {
            $mentee->status_id = MenteeStatuses::$statuses['rejected'];
            $mentee->save();
            $num_of_mentees_rejected += 1;
            // if the mentee was created after 1/1/2024, then also send the email
            if ($mentee->created_at > '2024-01-01') {
                echo "Sending email to " . $mentee->email . "\n";
                $mentee->notify(new MenteeStillUnmatched());
                $num_of_mentees_notified += 1;
            }
        }
        Log::info("Ran HandleUnmatchedMentees on:" . now()->toDateString() . ". Rejected " . $num_of_mentees_rejected . ", Notified: " . $num_of_mentees_notified . "\n");
        return 0;
    }
}
