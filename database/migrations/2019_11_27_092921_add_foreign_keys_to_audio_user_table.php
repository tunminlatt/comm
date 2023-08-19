<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToAudioUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audio_user', function (Blueprint $table) {
            $table->foreign('audio_id')->references('id')->on('audios')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audio_user', function (Blueprint $table) {
            $table->dropForeign(['audio_id']);
            $table->dropForeign(['user_id']);
        });
    }
}
