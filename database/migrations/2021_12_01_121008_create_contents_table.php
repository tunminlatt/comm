<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->uuid('station_id');
            $table->text('description')->nullable();
            $table->text('uploaded_by')->nullable(); // public key
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('contact_method')->nullable();
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('contents');
    }
}
