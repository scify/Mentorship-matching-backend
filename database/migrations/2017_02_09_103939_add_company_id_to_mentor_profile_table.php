<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompanyIdToMentorProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mentor_profile', function ($table) {
            $table->integer('company_id')->unsigned()->nullable();
        });

        Schema::table('mentor_profile', function ($table) {
            $table->foreign('company_id')->references('id')->on('company');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mentor_profile', function(Blueprint $table){
            $table->dropForeign('mentor_profile_company_id_foreign');
            $table->dropColumn('company_id');
        });
    }
}
