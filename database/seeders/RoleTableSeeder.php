<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('role')->delete();
        DB::table('role')->insert(array(
            array('id' => 1, 'title' => 'Administrator', 'description' => 'Admin with full rights'),
            array('id' => 2, 'title' => 'Matcher', 'description' => 'A Matcher can query Mentors and Mentees, match them and notify the Account Manager for the match.'),
            array('id' => 3, 'title' => 'Account Manager', 'description' => 'An Account Manager can query Mentors and Mentees and see the matches assigned to him. He audits the mentorship sessions.')
        ));
    }
}
