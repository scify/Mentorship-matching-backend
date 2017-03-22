<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UniversityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('university')->delete();
        DB::table('university')->insert(array(
            array('id'=> 1, 'name'=>'Εθνικό και Καποδιστριακό Πανεπιστήμιο Αθηνών'),
            array('id'=> 2, 'name'=>'Πανεπιστήμιο Πατρών'),
            array('id'=> 3, 'name'=>'Αριστοτέλειο Πανεπιστήμιο Θεσσαλονίκης'),
            array('id'=> 4, 'name'=>'Πανεπιστήμιο Αιγαίου'),
            array('id'=> 5, 'name'=>'Πανεπιστήμιο Πειραιώς'),
            array('id'=> 6, 'name'=>'Πανεπιστήμιο Μακεδονίας'),
            array('id'=> 7, 'name'=>'Εθνικό Μετσόβιο Πολυτεχνείο'),
            array('id'=> 8, 'name'=>'Οικονομικό Πανεπιστήμιο Αθηνών'),
            array('id'=> 9, 'name'=>'Πάντειο Πανεπιστήμιο'),
            array('id'=> 10, 'name'=>'ΤΕΙ Αθήνας'),
            array('id'=> 11, 'name'=>'ΤΕΙ Πειραιά'),
            array('id'=> 12, 'name'=>'Άλλο'),
        ));
    }
}
