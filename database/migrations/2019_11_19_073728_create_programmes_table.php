<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgrammesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programmes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->unique();
            $table->string('duration');
            $table->uuid('station_id');
            $table->bigInteger('uploaded_by')->unsigned(); // user_id
            $table->bigInteger('state_id')->unsigned()->default(1);
            $table->bigInteger('approved_by')->unsigned()->nullable(); // user_id
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
        Schema::dropIfExists('programmes');
    }
}
