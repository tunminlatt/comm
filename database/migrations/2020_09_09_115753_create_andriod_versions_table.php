<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAndriodVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('andriod_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('latest_version_code')->unique();
            $table->boolean('require_force_update');
            $table->integer('min_version_code');
            $table->string('play_store_link');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('andriod_versions');
    }
}
