<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialtyStatusTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (Schema::hasTable('specialty_status'))
            return;

        Schema::create('specialty_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status');
        });

        // immediate seeding!
        // BE CAREFUL!!! If you remove this seed from here, the `migrate` action will fail
        // as the next migration is going to use it
        DB::table('specialty_status')->delete();
        DB::table('specialty_status')->insert(array(
            array('id' => 1, 'status' => 'public'),
            array('id' => 2, 'status' => 'private'),
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('specialty_status');
    }
}
