<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class RenameTableMentorAdditionalSpecialtyToMentorSector extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::rename('mentor_additional_specialty', 'mentor_industry');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::rename('mentor_industry', 'mentor_additional_specialty');
    }
}
