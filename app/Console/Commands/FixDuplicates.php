<?php

namespace App\Console\Commands;

use App\Models\eloquent\MenteeProfile;
use App\Models\eloquent\MentorProfile;
use Illuminate\Console\Command;

class FixDuplicates extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle() {
        $handledEmails = [];
        $mentors = MenteeProfile::withTrashed()->whereIn('email', function ($query) {
            $query->select('email')->from('mentee_profile')->groupBy('email')->havingRaw('count(*) > 1');
        })->with('sessions')
            ->get();
        foreach ($mentors as $mentee) {
            if($mentee->sessions->count() > 0) {
                continue;
            }
            if (!in_array($mentee->email, $handledEmails)) {
                echo "Duplicate mentee email found: " . $mentee->email . "\n";
                $handledEmails[] = $mentee->email;
                $mentee->email = $mentee->email . '_duplicate_' . $mentee->id;
                $mentee->save();
                $mentee->delete();
            }
        }

        echo "Duplicates for mentees fixed successfully: " . count($handledEmails) . " duplicates fixed.\n\n";

        $handledEmails = [];
        $mentors = MentorProfile::withTrashed()->whereIn('email', function ($query) {
            $query->select('email')->from('mentor_profile')->groupBy('email')->havingRaw('count(*) > 1');
        })->with('sessions')
            ->get();
        foreach ($mentors as $mentor) {
            if($mentor->sessions->count() > 0) {
                continue;
            }
            if (!in_array($mentor->email, $handledEmails)) {
                echo "Duplicate mentor email found: " . $mentor->email . "\n";
                $handledEmails[] = $mentor->email;
                $mentor->email = $mentor->email . '_duplicate_' . $mentor->id;
                $mentor->save();
                $mentor->delete();
            }
        }

        echo "Duplicates for mentors fixed successfully: " . count($handledEmails) . " duplicates fixed.\n\n";

        return 0;
    }
}
