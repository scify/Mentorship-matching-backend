<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenteeProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mentee_profile', function ($table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->integer('age');
            $table->string('address');
            $table->integer('residence_id')->unsigned()->nullable();
            $table->foreign('residence_id')->references('id')->on('residence');
            $table->string('email');
            $table->string('linkedin_url');
            $table->string('phone');
            $table->string('cell_phone');
            $table->string('university_name');
            $table->string('university_department_name');
            $table->integer('university_graduation_year');
            $table->boolean('is_employed');
            $table->string('job_description');
            $table->integer('specialty_id')->unsigned()->nullable();
            $table->foreign('specialty_id')->references('id')->on('specialty');
            $table->string('job_experience');
            $table->string('expectations');
            $table->string('career_goals');
            $table->string('reference');
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
        Schema::dropIfExists('mentee_profile');
    }
}
