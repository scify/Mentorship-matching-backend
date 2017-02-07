<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();
        DB::table('users')->insert(array(
            array('id'=> 1, 'first_name'=>'Paul', 'last_name'=> 'Isaris', 'state_id' => 1, 'email'=>'paul@scify.org', 'password' => '$2y$10$AB2Q2QgPWuMXyVis.EgUau2F2TZK25lFe6SB/LyEMFzL38Qqjgy1e'),
            array('id'=> 2, 'first_name'=>'SciFY', 'last_name'=> 'Tester', 'state_id' => 1, 'email'=>'test@scify.org', 'password' => '$2y$10$AB2Q2QgPWuMXyVis.EgUau2F2TZK25lFe6SB/LyEMFzL38Qqjgy1e'),
            array('id'=> 3, 'first_name'=>'Alex', 'last_name'=> 'Tzoumas', 'state_id' => 1, 'email'=>'a.txoumas@scify.org', 'password' => '$2y$10$AB2Q2QgPWuMXyVis.EgUau2F2TZK25lFe6SB/LyEMFzL38Qqjgy1e')
        ));
    }
}
