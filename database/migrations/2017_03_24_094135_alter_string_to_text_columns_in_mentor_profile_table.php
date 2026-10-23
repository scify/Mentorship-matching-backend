<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AlterStringToTextColumnsInMentorProfileTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('mentor_profile', function ($table) {
            // nullable() has to be restated: since Laravel 11 change() drops any
            // attribute not listed, and this column is created as nullable in
            // 2017_02_07_084217_create_mentor_profile_table.
            $table->text('skills')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('mentor_profile', function ($table) {
            $table->string('skills')->nullable()->change();
        });
    }
}
