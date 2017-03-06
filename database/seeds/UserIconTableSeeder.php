<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserIconTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_icon')->delete();
        DB::table('user_icon')->insert(array(
            array('id'=> 1, 'title' => 'boy', 'path' => '/assets/img/boy.png', 'description' => 'This icon is used when the user is male'),
            array('id'=> 2, 'title' => 'girl', 'path' => '/assets/img/girl.png', 'description' => 'This icon is used when the user is female'),
        ));
    }
}
