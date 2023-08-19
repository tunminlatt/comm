<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToAudiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audios', function (Blueprint $table) {
            $table->foreign('station_id')->references('id')->on('stations')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('uploaded_by')->references('id')->on('volunteers')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audios', function (Blueprint $table) {
            $table->dropForeign(['station_id']);
            $table->dropForeign(['uploaded_by']);
        });
    }
}
