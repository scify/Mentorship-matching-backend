<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResidenceTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('residence')->delete();
        DB::table('residence')->insert([
            ['id' => 1, 'name' => 'Αθήνα'],
            ['id' => 2, 'name' => 'Θεσσαλονίκη'],
            ['id' => 3, 'name' => 'Κύπρος'],
            ['id' => 4, 'name' => 'Αλλού'],
        ]);
    }
}
