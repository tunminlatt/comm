<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScheduleColumnToProgramme extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programmes', function (Blueprint $table) {
            $table->timestamp('schedule')->nullable()->after('approved_by');
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
            $table->dropColumn('schedule');
        });
    }
}
