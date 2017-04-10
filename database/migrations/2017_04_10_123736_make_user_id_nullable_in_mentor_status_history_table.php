<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeUserIdNullableInMentorStatusHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mentor_status_history', function ($table) {
            $table->integer('user_id')->nullable()->unsigned()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mentor_status_history', function (Blueprint $table) {
            $table->integer('user_id')->nullable(false)->unsigned()->change();
        });
    }
}
