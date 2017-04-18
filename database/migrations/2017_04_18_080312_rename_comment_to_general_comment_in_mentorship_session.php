<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameCommentToGeneralCommentInMentorshipSession extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mentorship_session', function (Blueprint $table) {
            $table->renameColumn('comment', 'general_comment');
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
            $table->renameColumn('general_comment', 'comment');
        });
    }
}
