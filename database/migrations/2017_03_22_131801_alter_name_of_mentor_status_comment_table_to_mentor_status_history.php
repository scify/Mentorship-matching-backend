<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AlterNameOfMentorStatusCommentTableToMentorStatusHistory extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::rename('mentor_status_comment', 'mentor_status_history');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::rename('mentor_status_history', 'mentor_status_comment');
    }
}
