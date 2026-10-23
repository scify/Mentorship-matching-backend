<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MentorshipSessionStatusTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('mentorship_session_status_lookup')->delete();
        DB::table('mentorship_session_status_lookup')->insert([
            ['id' => 1, 'title' => 'pending', 'description' => 'Pending'],
            ['id' => 2, 'title' => 'introduction_sent', 'description' => 'Introductory mail was sent'],
            ['id' => 3, 'title' => 'available_mentee', 'description' => 'Mentee is available'],
            ['id' => 4, 'title' => 'available_mentor', 'description' => 'Mentor is available'],
            ['id' => 5, 'title' => 'started', 'description' => 'Started - Material Sent'],
            ['id' => 6, 'title' => 'first_meeting', 'description' => '1st meeting completed'],
            ['id' => 7, 'title' => 'second_meeting', 'description' => '2nd meeting completed'],
            ['id' => 8, 'title' => 'third_meeting', 'description' => '3rd meeting completed'],
            ['id' => 9, 'title' => 'fourth_meeting', 'description' => '4th meeting completed'],
            ['id' => 10, 'title' => 'evaluation_sent', 'description' => 'Completed - Evaluation sent'],
            ['id' => 11, 'title' => 'follow_up_sent', 'description' => 'Completed - Follow up sent'],
            ['id' => 12, 'title' => 'cancelled_mentee', 'description' => 'Cancelled by mentee'],
            ['id' => 13, 'title' => 'cancelled_mentor', 'description' => 'Cancelled by mentor'],
            ['id' => 14, 'title' => 'cancelled_acc_man', 'description' => 'Cancelled by account manager'],
        ]);
    }
}
