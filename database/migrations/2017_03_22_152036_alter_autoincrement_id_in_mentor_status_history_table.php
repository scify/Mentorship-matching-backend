<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAutoincrementIdInMentorStatusHistoryTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('mentor_status_history', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('mentor_status_history', function (Blueprint $table) {
            $table->increments('id')->first();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('mentor_status_history', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('mentor_status_history', function (Blueprint $table) {
            $table->integer('id')->unsigned()->first();
            $table->primary('id');
        });
    }
}
