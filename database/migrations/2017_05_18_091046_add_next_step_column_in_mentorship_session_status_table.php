<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNextStepColumnInMentorshipSessionStatusTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('mentorship_session_status_lookup', function (Blueprint $table) {
            $table->text('action_required')->after('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('mentorship_session_status_lookup', function (Blueprint $table) {
            $table->dropColumn('action_required');
        });
    }
}
