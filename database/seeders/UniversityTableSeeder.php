<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UniversityTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('university')->delete();
        DB::table('university')->insert([
            ['id' => 1, 'name' => 'Εθνικό και Καποδιστριακό Πανεπιστήμιο Αθηνών'],
            ['id' => 2, 'name' => 'Πανεπιστήμιο Πατρών'],
            ['id' => 3, 'name' => 'Αριστοτέλειο Πανεπιστήμιο Θεσσαλονίκης'],
            ['id' => 4, 'name' => 'Πανεπιστήμιο Αιγαίου'],
            ['id' => 5, 'name' => 'Πανεπιστήμιο Πειραιώς'],
            ['id' => 6, 'name' => 'Πανεπιστήμιο Μακεδονίας'],
            ['id' => 7, 'name' => 'Εθνικό Μετσόβιο Πολυτεχνείο'],
            ['id' => 8, 'name' => 'Οικονομικό Πανεπιστήμιο Αθηνών'],
            ['id' => 9, 'name' => 'Πάντειο Πανεπιστήμιο'],
            ['id' => 10, 'name' => 'ΤΕΙ Αθήνας'],
            ['id' => 11, 'name' => 'ΤΕΙ Πειραιά'],
            ['id' => 12, 'name' => 'Άλλο'],
        ]);
    }
}
