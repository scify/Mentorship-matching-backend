<?php

use Illuminate\Database\Seeder;

class ReferenceLookupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('reference')->delete();
        DB::table('reference')->insert(array(
            array('id'=> 1, 'name'=>'Μentee που έχει λάβει μέρος στο Job-Pairs'),
            array('id'=> 2, 'name'=>'Μentor που έχει λάβει μέρος στο Job-Pairs'),
            array('id'=> 3, 'name'=>'Εθελοντή/τρια του Job-Pairs'),
            array('id'=> 4, 'name'=>'Social Media (Facebook, Linkedin, Twitter κλπ.)'),
            array('id'=> 5, 'name'=>'Άρθρο στα Μέσα Μαζικής Ενημέρωσης (blogs, TV, περιοδικά κλπ.)'),
            array('id'=> 6, 'name'=>'Αναζήτηση στο internet'),
            array('id'=> 7, 'name'=>'Άλλο')
        ));
    }
}
