<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTextToCommentInMentorStatusCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mentor_status_comment', function (Blueprint $table) {
            $table->renameColumn('text', 'comment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mentor_status_comment', function (Blueprint $table) {
            $table->renameColumn('comment', 'text');
        });
    }
}
