<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenteeStatusHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mentee_status_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('mentee_profile_id')->unsigned();
            $table->foreign('mentee_profile_id')->references('id')->on('mentee_profile');
            $table->integer('mentee_status_id')->unsigned();
            $table->foreign('mentee_status_id')->references('id')->on('mentee_status_lookup');
            $table->text('comment')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mentee_status_history');
    }
}
