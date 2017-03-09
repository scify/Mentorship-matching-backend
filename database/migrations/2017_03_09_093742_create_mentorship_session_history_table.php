<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMentorshipSessionHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mentorship_session_history', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('mentorship_session_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('status_id');
            $table->text('comment');
            $table->foreign('mentorship_session_id')->references('id')->on('mentorship_session');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('status_id')->references('id')->on('mentorship_session_status_lookup');
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
        Schema::dropIfExists('mentorship_session_history');
    }
}
