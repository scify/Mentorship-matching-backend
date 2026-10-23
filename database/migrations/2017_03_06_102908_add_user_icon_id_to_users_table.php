<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddUserIconIdToUsersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('users', function ($table) {
            $table->integer('user_icon_id')->unsigned()->nullable()->after('last_name');
            $table->foreign('user_icon_id')->references('id')->on('user_icon');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('users', function ($table) {
            $table->dropForeign('users_user_icon_id_foreign');
            $table->dropColumn('user_icon_id');
        });
    }
}
