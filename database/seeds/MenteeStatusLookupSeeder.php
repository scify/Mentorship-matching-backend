<?php

use Illuminate\Database\Seeder;

class MenteeStatusLookupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mentee_status_lookup')->delete();
        DB::table('mentee_status_lookup')->insert(array(
            array('id'=> 1, 'status' => 'available', 'description' => 'Available'),
            array('id'=> 2, 'status' => 'matched', 'description' => 'Matched'),
            array('id'=> 3, 'status' => 'completed', 'description' => 'Completed'),
            array('id'=> 4, 'status' => 'rejected', 'description' => 'Rejected'),
            array('id'=> 5, 'status' => 'black_listed', 'description' => 'Black listed'),
            array('id'=> 6, 'status' => 'followed_up', 'description' => 'Followed up'),
        ));
    }
}
