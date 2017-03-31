<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MentorshipSessionStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mentorship_session_status_lookup')->delete();
        DB::table('mentorship_session_status_lookup')->insert(array(
            array('id'=> 1, 'title' => 'pending', 'description' => 'Pending'),
            array('id'=> 2, 'title' => 'available_mentee', 'description' => 'Mentee is available'),
            array('id'=> 3, 'title' => 'available_mentor', 'description' => 'Mentor is available'),
            array('id'=> 4, 'title' => 'started', 'description' => 'Started - Material Sent'),
            array('id'=> 5, 'title' => 'first_meeting', 'description' => '1st meeting completed'),
            array('id'=> 6, 'title' => 'second_meeting', 'description' => '2nd meeting completed'),
            array('id'=> 7, 'title' => 'third_meeting', 'description' => '3rd meeting completed'),
            array('id'=> 8, 'title' => 'fourth_meeting', 'description' => '4th meeting completed'),
            array('id'=> 9, 'title' => 'evaluation_sent', 'description' => 'Completed - Evaluation sent'),
            array('id'=> 10, 'title' => 'follow_up_sent', 'description' => 'Completed - Follow up sent'),
            array('id'=> 11, 'title' => 'cancelled', 'description' => 'Cancelled'),
        ));
    }
}
