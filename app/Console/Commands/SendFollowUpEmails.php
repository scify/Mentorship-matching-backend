<?php

namespace App\Console\Commands;

use App\BusinessLogicLayer\managers\MentorshipSessionManager;
use App\Notifications\MenteeFollowUp;
use App\Utils\MentorshipSessionStatuses;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendFollowUpEmails extends Command {
    protected $sessionManager;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:follow-up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends follow up emails to mentors and mentees when the session was completed 3 months ago';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->sessionManager = new MentorshipSessionManager();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int {
        $sessions = $this->sessionManager->getSessionsForFollowUp();
        if (count($sessions) > 0) {
            $mentorshipSessionStatuses = new MentorshipSessionStatuses();
            $sessionIdsForInfoMsg = [];
            foreach ($sessions as $session) {
                $session->mentee->notify(new MenteeFollowUp($session));
                // set to 'follow up sent' status
                $sessionEditInfo = array('mentorship_session_id' => $session->id,
                    'status_id' => $mentorshipSessionStatuses::getCompletedSessionStatuses()[1]
                );
                $this->sessionManager->editMentorshipSession($sessionEditInfo);
                $sessionIdsForInfoMsg[] = $session->id;
            }
            Log::info(now()->toDateString() . ": Follow ups were sent for sessions with ids: " . implode(", ", $sessionIdsForInfoMsg));
        } else {
            Log::info(now()->toDateString() . ": No follow ups sent today!");
        }
        return 0;
    }
}
