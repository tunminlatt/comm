<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeForeignKeysToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['user_type_id']);
            $table->dropForeign(['station_id']);

            $table->foreign('user_type_id')
                ->references('id')
                ->on('user_types')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('station_id')
                ->references('id')
                ->on('stations')
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
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
