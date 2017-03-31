<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStatusIdSetDefaultTo1OnMentorshipSession extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mentorship_session', function (Blueprint $table) {
            $table->integer('status_id')->unsigned()->default(1)->change();
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
            $table->integer('status_id')->unsigned()->default(null)->change();
        });
    }
}
