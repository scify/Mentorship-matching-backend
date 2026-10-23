<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStatusIdSetDefaultTo1OnMentorshipSessionHistory extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('mentorship_session_history', function (Blueprint $table) {
            $table->integer('status_id')->unsigned()->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('mentorship_session_history', function (Blueprint $table) {
            $table->integer('status_id')->unsigned()->default(null)->change();
        });
    }
}
