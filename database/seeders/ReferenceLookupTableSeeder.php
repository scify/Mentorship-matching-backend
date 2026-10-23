<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferenceLookupTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('reference')->delete();
        DB::table('reference')->insert([
            ['id' => 1, 'name' => 'Μentee που έχει λάβει μέρος στο Job-Pairs'],
            ['id' => 2, 'name' => 'Μentor που έχει λάβει μέρος στο Job-Pairs'],
            ['id' => 3, 'name' => 'Εθελοντή/τρια του Job-Pairs'],
            ['id' => 4, 'name' => 'Social Media (Facebook, Linkedin, Twitter κλπ.)'],
            ['id' => 5, 'name' => 'Άρθρο στα Μέσα Μαζικής Ενημέρωσης (blogs, TV, περιοδικά κλπ.)'],
            ['id' => 6, 'name' => 'Αναζήτηση στο internet'],
            ['id' => 7, 'name' => 'Άλλο'],
        ]);
    }
}
