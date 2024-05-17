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
        DB::table('specialty')->insert(array(
            array('id' => 1, 'name' => 'Διοίκηση Ανθρωπίνων Πόρων'),
            array('id' => 2, 'name' => 'Πληροφορική / Τεχνολογία'),
            array('id' => 3, 'name' => 'Επικοινωνία / Δημόσιες Σχέσεις'),
            array('id' => 4, 'name' => 'Ηλεκτρολόγος Μηχανικός'),
            array('id' => 5, 'name' => 'Χημικός Μηχανικός'),
            array('id' => 6, 'name' => 'Οικονομικά / Λογιστική'),
            array('id' => 7, 'name' => 'Marketing'),
            array('id' => 8, 'name' => 'Logistics'),
            array('id' => 9, 'name' => 'Μηχανολόγος Μηχανικός'),
            array('id' => 10, 'name' => 'Πωλήσεις')
        ));
    }
}
