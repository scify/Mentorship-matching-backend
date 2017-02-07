<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMentorAdditionalspecialtyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mentor_additional_specialty', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mentor_profile_id')->unsigned();
            $table->foreign('mentor_profile_id')->references('id')->on('mentor_profile');

            $table->integer('additional_specialty_id')->unsigned();
            $table->foreign('additional_specialty_id')->references('id')->on('additional_specialty');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mentor_additional_specialty');
    }
}
