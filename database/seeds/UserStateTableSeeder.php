<?php

use Illuminate\Database\Seeder;

class UserStateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_state')->delete();
        DB::table('user_state')->insert(array(
            array('id'=> 1, 'title'=>'Activated','description'=>'User with access to the platform'),
            array('id'=> 2, 'title'=>'Deactivated','description'=>'User with no access to the platform')
        ));
    }
}
