<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EducationLevelTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('education_level')->delete();
        DB::table('education_level')->insert([
            ['id'=> 1, 'name'=>'Πτυχίο ΑΕΙ'],
            ['id'=> 2, 'name'=>'Πτυχίο ΤΕΙ'],
            ['id'=> 3, 'name'=>'Πτυχίο Ανώτατης Εκπαίδευσης (5ετείς σχολές, στρατιωτικές σχολές κλπ.)'],
            ['id'=> 4, 'name'=>'Μεταπτυχιακό δίπλωμα'],
            ['id'=> 5, 'name'=>'Διδακτορικό δίπλωμα'],
            ['id'=> 6, 'name'=>'Δεν έχω ολοκληρώσει τις προπτυχιακές σπουδές'],
        ]);
    }
}
