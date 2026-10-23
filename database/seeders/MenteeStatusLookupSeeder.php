<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenteeStatusLookupSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('mentee_status_lookup')->delete();
        DB::table('mentee_status_lookup')->insert([
            ['id' => 1, 'status' => 'available', 'description' => 'Available'],
            ['id' => 2, 'status' => 'matched', 'description' => 'Matched'],
            ['id' => 3, 'status' => 'completed', 'description' => 'Completed'],
            ['id' => 4, 'status' => 'rejected', 'description' => 'Rejected'],
            ['id' => 5, 'status' => 'black_listed', 'description' => 'Black listed'],
            ['id' => 6, 'status' => 'followed_up', 'description' => 'Followed up'],
        ]);
    }
}
