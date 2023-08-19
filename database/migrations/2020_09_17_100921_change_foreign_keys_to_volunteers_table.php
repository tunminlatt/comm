<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeForeignKeysToVolunteersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->dropForeign(['station_id']);
            $table->dropForeign(['language_id']);

            $table->foreign('station_id')
                ->references('id')
                ->on('stations')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('language_id')
                ->references('id')
                ->on('languages')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('volunteers', function (Blueprint $table) {
            //
        });
    }
}
