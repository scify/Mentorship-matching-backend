<?php

use Illuminate\Database\Seeder;

class UserRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_role')->delete();
        DB::table('user_role')->insert(array(
            array('id'=> 1, 'user_id'=>1,'role_id'=>1),
        ));
    }
}
