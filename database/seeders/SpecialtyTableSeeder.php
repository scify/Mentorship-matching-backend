<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialtyTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('specialty')->delete();
        DB::table('specialty')->insert([
            ['id' => 1, 'name' => 'Διοίκηση Ανθρωπίνων Πόρων'],
            ['id' => 2, 'name' => 'Πληροφορική / Τεχνολογία'],
            ['id' => 3, 'name' => 'Επικοινωνία / Δημόσιες Σχέσεις'],
            ['id' => 4, 'name' => 'Ηλεκτρολόγος Μηχανικός'],
            ['id' => 5, 'name' => 'Χημικός Μηχανικός'],
            ['id' => 6, 'name' => 'Οικονομικά / Λογιστική'],
            ['id' => 7, 'name' => 'Marketing'],
            ['id' => 8, 'name' => 'Logistics'],
            ['id' => 9, 'name' => 'Μηχανολόγος Μηχανικός'],
            ['id' => 10, 'name' => 'Πωλήσεις'],
        ]);
    }
}
