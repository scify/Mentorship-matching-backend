<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddStatusIdToMentorStatusHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mentor_status_history', function (Blueprint $table) {
            $table->integer('mentor_status_id')->after('mentor_profile_id')->unsigned();
            $table->foreign('mentor_status_id')->references('id')->on('mentor_status_lookup');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mentor_status_history', function (Blueprint $table) {
            $table->dropForeign('mentor_status_history_mentor_status_id_foreign');
            $table->dropColumn('mentor_status_id');
        });
    }
}
