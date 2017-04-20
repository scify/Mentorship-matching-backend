<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCvFileNameToMentorProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mentor_profile', function (Blueprint $table) {
            $table->string('cv_file_name')->after('skills')->nullable();
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
            $table->dropColumn('cv_file_name');
        });
    }
}
