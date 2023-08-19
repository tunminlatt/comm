<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublicKeyColumnInStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stations', function (Blueprint $table) {
            $table->string('signal')->nullable()->after('messenger_link');
            $table->string('viber')->nullable()->after('signal');
            $table->string('whats_app')->nullable()->after('viber');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stations', function (Blueprint $table) {
            $table->dropColumn('signal');
            $table->dropColumn('viber');
            $table->dropColumn('whats_app');
        });
    }
}
