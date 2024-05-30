<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $DEFAULT_PASSWORD = 'test1234';
        DB::table('users')->updateOrInsert(['id' => 1],
            array('id' => 1, 'first_name' => 'Paul', 'last_name' => 'Isaris', 'state_id' => 1, 'email' => 'paul@scify.org', 'password' => bcrypt($DEFAULT_PASSWORD)),
        );
        DB::table('users')->updateOrInsert(['id' => 2],
            array('id' => 2, 'first_name' => 'SciFY', 'last_name' => 'Tester', 'state_id' => 1, 'email' => 'test@scify.org', 'password' => bcrypt($DEFAULT_PASSWORD)));
        DB::table('users')->updateOrInsert(['id' => 3],
            array('id' => 3, 'first_name' => 'Alex', 'last_name' => 'Tzoumas', 'state_id' => 1, 'email' => 'a.tzoumas@scify.org', 'password' => bcrypt($DEFAULT_PASSWORD)));
    }
}
