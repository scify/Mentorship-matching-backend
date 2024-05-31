<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeEmailUniqueInMentorsAndMentees extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('mentee_profile', function (Blueprint $table) {
            $table->unique('email');
        });

        Schema::table('mentor_profile', function (Blueprint $table) {
            $table->unique('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('mentee_profile', function (Blueprint $table) {
            $table->dropUnique('email');
        });

        Schema::table('mentor_profile', function (Blueprint $table) {
            $table->dropUnique('email');
        });
    }
}
