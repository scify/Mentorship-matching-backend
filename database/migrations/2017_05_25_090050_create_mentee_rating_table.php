<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenteeRatingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mentee_rating', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rating');
            $table->text('rating_description');
            $table->timestamps();
            $table->integer('mentee_id')->unsigned();
            $table->foreign('mentee_id')->references('id')->on('mentee_profile');
            $table->integer('session_id')->unsigned();
            $table->foreign('session_id')->references('id')->on('mentorship_session');
            $table->integer('rated_by_id')->unsigned();
            $table->foreign('rated_by_id')->references('id')->on('mentor_profile');
            $table->unique(array('mentee_id', 'session_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mentee_rating');
    }
}
