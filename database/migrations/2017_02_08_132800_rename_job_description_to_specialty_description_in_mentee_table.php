<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameJobDescriptionToSpecialtyDescriptionInMenteeTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('mentee_profile', function (Blueprint $table) {
            $table->renameColumn('job_experience', 'specialty_experience');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('mentee_profile', function (Blueprint $table) {
            $table->renameColumn('specialty_experience', 'job_experience');
        });
    }
}
