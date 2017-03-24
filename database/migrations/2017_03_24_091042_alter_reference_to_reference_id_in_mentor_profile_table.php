<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterReferenceToReferenceIdInMentorProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mentor_profile', function (Blueprint $table) {
            $table->dropColumn('reference');
            $table->integer('reference_id')->after('creator_user_id')->nullable()->unsigned();
            $table->foreign('reference_id')->references('id')->on('reference');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mentor_profile', function (Blueprint $table) {
            $table->dropForeign('mentor_profile_reference_id_foreign');
            $table->dropColumn('reference_id');
            $table->string('reference')->after('creator_user_id')->nullable();
        });
    }
}
