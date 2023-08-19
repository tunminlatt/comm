<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeForeignKeysToProgrammesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programmes', function (Blueprint $table) {
            $table->dropForeign(['station_id']);
            $table->dropForeign(['uploaded_by']);
            $table->dropForeign(['state_id']);
            $table->dropForeign(['approved_by']);

            $table->foreign('station_id')
                ->references('id')
                ->on('stations')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('uploaded_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('state_id')
                ->references('id')
                ->on('states')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
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
        Schema::table('programmes', function (Blueprint $table) {
            //
        });
    }
}
