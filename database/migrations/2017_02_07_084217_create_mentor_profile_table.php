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
            $table->integer('age')->nullable();
            $table->string('address')->nullable();
            $table->integer('residence_id')->unsigned()->nullable();
            $table->foreign('residence_id')->references('id')->on('residence');
            $table->string('email');
            $table->string('linkedin_url')->nullable();
            $table->string('phone')->nullable();
            $table->string('cell_phone')->nullable();
            $table->string('company')->nullable();
            $table->string('company_sector')->nullable();
            $table->string('job_position')->nullable();
            $table->string('job_experience_years')->nullable();
            $table->string('university_name')->nullable();
            $table->string('university_department_name')->nullable();
            $table->string('skills')->nullable();
            $table->string('reference')->nullable();
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
        Schema::dropIfExists('mentor_profile');
    }
}
