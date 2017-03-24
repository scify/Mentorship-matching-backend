<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStringToTextColumnsInMenteeProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mentee_profile', function ($table) {
            $table->text('job_description')->change();
            $table->text('specialty_experience')->change();
            $table->text('expectations')->change();
            $table->text('career_goals')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mentee_profile', function ($table) {
            $table->string('job_description')->change();
            $table->string('specialty_experience')->change();
            $table->string('expectations')->change();
            $table->string('career_goals')->change();
        });
    }
}
