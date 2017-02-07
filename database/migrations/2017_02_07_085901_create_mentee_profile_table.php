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
            $table->integer('age')->nullable();
            $table->string('address')->nullable();
            $table->integer('residence_id')->unsigned()->nullable();
            $table->foreign('residence_id')->references('id')->on('residence');
            $table->string('email')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('phone')->nullable();
            $table->string('cell_phone')->nullable();
            $table->string('university_name')->nullable();
            $table->string('university_department_name')->nullable();
            $table->integer('university_graduation_year')->nullable();
            $table->boolean('is_employed')->nullable();
            $table->string('job_description')->nullable();
            $table->integer('specialty_id')->unsigned()->nullable();
            $table->foreign('specialty_id')->references('id')->on('specialty');
            $table->string('job_experience')->nullable();
            $table->string('expectations')->nullable();
            $table->string('career_goals')->nullable();
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
        Schema::dropIfExists('mentee_profile');
    }
}
