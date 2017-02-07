<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMentorProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mentor_profile', function ($table) {
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
            $table->string('company');
            $table->string('company_sector');
            $table->string('job_position');
            $table->string('job_experience_years');
            $table->string('university_name');
            $table->string('university_department_name');
            $table->string('skills');
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
        Schema::dropIfExists('mentor_profile');
    }
}
