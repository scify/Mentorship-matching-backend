<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToSpecialtyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('specialty', function (Blueprint $table) {
            $table->integer('status_id')->unsigned()->after('name')->default(1);
            // BE CAREFUL!!! you might need to comment below line and `$table->dropForeign(['status_id']);`
            // from down() function as well in order to get it working. This is caused because the seeder
            // hasn't been run yet. Drop the `status_id` column from the `specialty` table, run the seeder
            // by doing `php artisan db:seed --class=SpecialtyStatusTableSeeder` and then create manually
            // the foreign key to the `specialty_status` table.
            $table->foreign('status_id')->references('id')->on('specialty_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('specialty', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
        });
    }
}
