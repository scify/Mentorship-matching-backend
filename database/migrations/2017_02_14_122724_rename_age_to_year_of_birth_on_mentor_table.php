<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class RenameAgeToYearOfBirthOnMentorTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('mentee_profile', function ($table) {
            $table->renameColumn('age', 'year_of_birth');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('mentee_profile', function ($table) {
            $table->renameColumn('year_of_birth', 'age');
        });
    }
}
