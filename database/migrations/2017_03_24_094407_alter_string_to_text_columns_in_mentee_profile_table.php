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
            // nullable() has to be restated: since Laravel 11 change() drops any
            // attribute not listed, and these columns are all created as
            // nullable in 2017_02_07_084832_create_mentee_profile_table.
            $table->text('job_description')->nullable()->change();
            $table->text('specialty_experience')->nullable()->change();
            $table->text('expectations')->nullable()->change();
            $table->text('career_goals')->nullable()->change();
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
            $table->string('job_description')->nullable()->change();
            $table->string('specialty_experience')->nullable()->change();
            $table->string('expectations')->nullable()->change();
            $table->string('career_goals')->nullable()->change();
        });
    }
}
