<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccountManagerIdToCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company', function ($table) {
            $table->integer('account_manager_id')->unsigned()->nullable();
        });

        Schema::table('company', function ($table) {
            $table->foreign('account_manager_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company', function(Blueprint $table){
            $table->dropForeign('company_account_manager_id_foreign');
            $table->dropColumn('account_manager_id');
        });
    }
}
