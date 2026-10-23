<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class MakeColumnsNullableInMenteeProfileTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('mentee_profile', function ($table) {
            $table->integer('university_graduation_year')->nullable()->unsigned()->change();
            $table->integer('year_of_birth')->nullable()->unsigned()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('mentee_profile', function ($table) {
            $table->integer('university_graduation_year')->nullable(false)->unsigned()->change();
            $table->integer('year_of_birth')->nullable(false)->unsigned()->change();
        });
    }
}
