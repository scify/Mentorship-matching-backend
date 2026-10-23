<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenteeSpecialtyTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (Schema::hasTable('mentee_specialty')) {
            return;
        }
        Schema::create('mentee_specialty', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mentee_profile_id')->unsigned();
            $table->foreign('mentee_profile_id')->references('id')->on('mentee_profile');

            $table->integer('specialty_id')->unsigned();
            $table->foreign('specialty_id')->references('id')->on('specialty');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
    }
}
