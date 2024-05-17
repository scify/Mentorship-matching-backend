<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MentorStatusLookupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mentor_status_lookup')->delete();
        DB::table('mentor_status_lookup')->insert(array(
            array('id'=> 1, 'status' => 'available', 'description' => 'Available'),
            array('id'=> 2, 'status' => 'not_available_for_mentorship', 'description' => 'Not available for mentorship'),
            array('id'=> 3, 'status' => 'matched', 'description' => 'Matched'),
            array('id'=> 4, 'status' => 'black_listed', 'description' => 'Black listed'),
        ));
    }
}
