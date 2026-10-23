<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndustryTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('industry')->delete();
        DB::table('industry')->insert([
            ['id' => 1, 'name' => 'Ενέργεια'],
            ['id' => 2, 'name' => 'Επιχειρηματικότητα / Start-ups'],
            ['id' => 3, 'name' => 'Καταναλωτικά'],
            ['id' => 4, 'name' => 'Κατασκευές'],
            ['id' => 5, 'name' => 'Μεταφορές'],
            ['id' => 6, 'name' => 'Ναυτιλιακά'],
            ['id' => 7, 'name' => 'Συμβουλευτικός κλάδος'],
            ['id' => 8, 'name' => 'Τηλεπικοινωνίες'],
            ['id' => 9, 'name' => 'Τουρισμός'],
            ['id' => 10, 'name' => 'Τραπεζικά / Χρηματο-οικονομικά'],
            ['id' => 11, 'name' => 'Τρόφιμα'],
            ['id' => 12, 'name' => 'Φάρμακο / Χημικά'],
        ]);
    }
}
