<?php

use Illuminate\Database\Seeder;

class AdditionalSpecialtyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('additional_specialty')->delete();
        DB::table('additional_specialty')->insert(array(
            array('id'=> 1, 'name'=>'Ενέργεια'),
            array('id'=> 2, 'name'=>'Επιχειρηματικότητα / Start-ups'),
            array('id'=> 3, 'name'=>'Καταναλωτικά'),
            array('id'=> 4, 'name'=>'Κατασκευές'),
            array('id'=> 5, 'name'=>'Μεταφορές'),
            array('id'=> 6, 'name'=>'Ναυτιλιακά'),
            array('id'=> 7, 'name'=>'Συμβουλευτικός κλάδος'),
            array('id'=> 8, 'name'=>'Τηλεπικοινωνίες'),
            array('id'=> 9, 'name'=>'Τουρισμός'),
            array('id'=> 10, 'name'=>'Τραπεζικά / Χρηματο-οικονομικά'),
            array('id'=> 11, 'name'=>'Τρόφιμα'),
            array('id'=> 12, 'name'=>'Φάρμακο / Χημικά')
        ));
    }
}
