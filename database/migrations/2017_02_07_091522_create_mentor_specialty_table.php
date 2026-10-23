<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMentorSpecialtyTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('mentor_specialty', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mentor_profile_id')->unsigned();
            $table->foreign('mentor_profile_id')->references('id')->on('mentor_profile');

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
        Schema::dropIfExists('mentor_specialty');
    }
}
