<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMentorshipSessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mentorship_session', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('mentor_profile_id');
            $table->unsignedInteger('mentee_profile_id');
            $table->unsignedInteger('account_manager_id');
            $table->unsignedInteger('matcher_id');
            $table->foreign('mentor_profile_id')->references('id')->on('mentor_profile');
            $table->foreign('mentee_profile_id')->references('id')->on('mentee_profile');
            $table->foreign('account_manager_id')->references('id')->on('users');
            $table->foreign('matcher_id')->references('id')->on('users');
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
        Schema::dropIfExists('mentorship_session');
    }
}
