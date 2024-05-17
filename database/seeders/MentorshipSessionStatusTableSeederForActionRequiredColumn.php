<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MentorshipSessionStatusTableSeederForActionRequiredColumn extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('mentorship_session_status_lookup')
            ->where('id', 1)
            ->update(['action_required' => 'Confirm availability for mentee and mentor. Clicking the button below will send first an email to mentee (asking for his/her availability) and then to mentor.']);

        DB::table('mentorship_session_status_lookup')
            ->where('id', 4)
            ->update(['action_required' => 'Both mentor and mentee are available. You should send an introduction email between the 2 parties. If you have already done it, update the status below to "Introduction email & related info"']);

    }
}
