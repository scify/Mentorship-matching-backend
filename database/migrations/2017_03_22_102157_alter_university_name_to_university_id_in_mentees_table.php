<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUniversityNameToUniversityIdInMenteesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mentee_profile', function (Blueprint $table) {
            $table->dropColumn('university_name');
            $table->integer('university_id')->after('cell_phone')->nullable()->unsigned();
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
        Schema::table('mentee_profile', function (Blueprint $table) {
            $table->dropForeign('mentee_profile_university_id_foreign');
            $table->dropColumn('university_id');
            $table->string('university_name')->after('cell_phone')->nullable();
        });
    }
}
