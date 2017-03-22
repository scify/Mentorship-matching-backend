<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EducationLevelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('education_level')->delete();
        DB::table('education_level')->insert(array(
            array('id'=> 1, 'name'=>'Πτυχίο ΑΕΙ'),
            array('id'=> 2, 'name'=>'Πτυχίο ΤΕΙ'),
            array('id'=> 3, 'name'=>'Πτυχίο Ανώτατης Εκπαίδευσης (5ετείς σχολές, στρατιωτικές σχολές κλπ.)'),
            array('id'=> 4, 'name'=>'Μεταπτυχιακό δίπλωμα'),
            array('id'=> 5, 'name'=>'Διδακτορικό δίπλωμα'),
            array('id'=> 6, 'name'=>'Δεν έχω ολοκληρώσει τις προπτυχιακές σπουδές'),
        ));
    }
}
