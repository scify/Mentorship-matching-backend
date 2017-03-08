<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIsAvailableToStatusInMentorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mentor_profile', function (Blueprint $table) {
            $table->dropColumn('is_available');
            $table->integer('status_id')->unsigned()->default(1);
            $table->foreign('status_id')->references('id')->on('mentor_status_lookup');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mentor_profile', function (Blueprint $table) {
            $table->dropForeign('mentor_profile_status_id_foreign');
            $table->dropColumn('status_id');
            $table->boolean('is_available')->default(true);
        });
    }
}
