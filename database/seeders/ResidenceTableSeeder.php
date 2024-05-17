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
        DB::table('residence')->insert(array(
            array('id' => 1, 'name' => 'Αθήνα'),
            array('id' => 2, 'name' => 'Θεσσαλονίκη'),
            array('id' => 3, 'name' => 'Κύπρος'),
            array('id' => 4, 'name' => 'Αλλού')
        ));
    }
}
