<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEducationLevelIdAndUniversityIdToMentorProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mentor_profile', function (Blueprint $table) {
            $table->integer("education_level_id")->after('job_experience_years')->nullable()->unsigned();
            $table->integer("university_id")->after('education_level_id')->nullable()->unsigned();
            $table->foreign('education_level_id')->references('id')->on('education_level');
            $table->foreign('university_id')->references('id')->on('university');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mentor_profile', function (Blueprint $table) {
            $table->dropForeign('mentor_profile_university_id_foreign');
            $table->dropForeign('mentor_profile_education_level_id_foreign');
            $table->dropColumn('university_id');
            $table->dropColumn('education_level_id');
        });
    }
}
