<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MentorshipSessionStatusTableSeederPatch extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('mentorship_session_status_lookup')
            ->where('id', 1)
            ->update(['description' => 'Assigned to Account Manager']);

        DB::table('mentorship_session_status_lookup')
            ->where('id', 2)
            ->update(['description' => 'Emailed Mentee and Mentor to confirm availability']);

        DB::table('mentorship_session_status_lookup')
            ->where('id', 5)
            ->update(['description' => 'Introduction email & related info']);
    }
}
