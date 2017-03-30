<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusIdToMentorshipSessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mentorship_session', function (Blueprint $table) {
            $table->integer('status_id')->unsigned()->after("matcher_id")->default(null);
            $table->foreign('status_id')->references('id')->on('mentorship_session_status_lookup');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mentorship_session', function (Blueprint $table) {
            $table->dropForeign('mentorship_session_status_id_foreign');
            $table->dropColumn('status_id');
        });
    }
}
