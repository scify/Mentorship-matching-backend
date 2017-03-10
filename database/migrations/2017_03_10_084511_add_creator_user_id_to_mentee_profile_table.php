<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatorUserIdToMenteeProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mentee_profile', function ($table) {
            $table->integer('creator_user_id')->unsigned()->nullable()->after('id');
            $table->foreign('creator_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mentee_profile', function ($table) {
            $table->dropForeign('mentee_profile_creator_user_id_foreign');
            $table->dropColumn('creator_user_id');
        });
    }
}
