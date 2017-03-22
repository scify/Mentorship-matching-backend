<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddEducationLevelIdToMenteeProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mentee_profile', function (Blueprint $table) {
            $table->integer('education_level_id')->after('cell_phone')->nullable()->unsigned();
            $table->foreign('education_level_id')->references('id')->on('education_level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mentee_profile', function (Blueprint $table) {
            $table->dropForeign('mentee_profile_education_level_id_foreign');
            $table->dropColumn('education_level_id');
        });
    }
}
