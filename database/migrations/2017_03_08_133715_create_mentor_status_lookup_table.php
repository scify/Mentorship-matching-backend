<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMentorStatusLookupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mentor_status_comment', function (Blueprint $table) {
            $table->integer('id')->unsigned();
            $table->primary('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('mentor_profile_id')->unsigned();
            $table->foreign('mentor_profile_id')->references('id')->on('mentor_profile');
            $table->text('text')->nullable();
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
        Schema::dropIfExists('mentor_status_lookup');
    }
}
