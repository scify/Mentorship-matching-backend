<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddCreatorUserIdToMentorProfileTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('mentor_profile', function ($table) {
            $table->integer('creator_user_id')->unsigned()->nullable()->after('id');
            $table->foreign('creator_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('mentor_profile', function ($table) {
            $table->dropForeign('mentor_profile_creator_user_id_foreign');
            $table->dropColumn('creator_user_id');
        });
    }
}
