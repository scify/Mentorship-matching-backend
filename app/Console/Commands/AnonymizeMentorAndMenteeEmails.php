<?php

namespace App\Console\Commands;

use App\Models\eloquent\MenteeProfile;
use App\Models\eloquent\MentorProfile;
use Illuminate\Console\Command;

class AnonymizeMentorAndMenteeEmails extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anonymize:emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Anonymizes the emails of mentors and mentees by adding a random string to the email address.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int {

        // this command is to be used only in the local environment (for development purposes).
        if (app()->environment() !== 'local') {
            $this->error("This command can only be used in the local environment.");
            return 1;
        }
        $this->anonymizeMentors();
        $this->anonymizeMentees();
        return 0;
    }

    private function anonymizeMentors() {
        $mentors = MentorProfile::withTrashed()->get();
        foreach ($mentors as $mentor) {
            $this->anonymizeEmail($mentor);
        }
        $this->info("Anonymized emails for " . count($mentors) . " mentors.");
    }

    private function anonymizeMentees() {
        $mentees = MenteeProfile::withTrashed()->get();
        foreach ($mentees as $mentee) {
            $this->anonymizeEmail($mentee);
        }
        $this->info("Anonymized emails for " . count($mentees) . " mentees.");
    }

    private function anonymizeEmail($profile) {
        // get the part of the email after the '@' symbol
        $emailParts = explode('@', $profile->email);
        // create a new email by adding a random string to the email address
        $profile->email = uniqid() . '@' . $emailParts[1];
        $profile->save();
    }

}