<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeForeignKeysToAudioUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audio_user', function (Blueprint $table) {
            $table->dropForeign(['audio_id']);
            $table->dropForeign(['user_id']);

            $table->foreign('audio_id')
                ->references('id')
                ->on('audios')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('user_id')
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
        Schema::table('audio_user', function (Blueprint $table) {
            //
        });
    }
}
