<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMentorRatingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mentor_rating', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rating');
            $table->text('rating_description');
            $table->timestamps();
            $table->integer('mentor_id')->unsigned();
            $table->foreign('mentor_id')->references('id')->on('mentor_profile');
            $table->integer('session_id')->unsigned();
            $table->foreign('session_id')->references('id')->on('mentorship_session');
            $table->integer('rated_by_id')->unsigned();
            $table->foreign('rated_by_id')->references('id')->on('mentee_profile');
            $table->unique(array('mentor_id', 'session_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mentor_rating');
    }
}
