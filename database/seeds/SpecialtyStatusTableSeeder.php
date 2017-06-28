<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialtyStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('specialty_status')->delete();
        DB::table('specialty_status')->insert(array(
            array('id'=> 1, 'status'=>'public'),
            array('id'=> 2, 'status'=>'private'),
        ));
    }
}
