<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExperimentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('experiments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('owner_id');
            $table->string('name', 120);
            $table->string('uid', 80);
            $table->string('alias', 200);
            $table->string('config', 5000);
            $table->boolean('is_enabled');
            $table->timestamps();

            $table->index('owner_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('experiments');
    }
}
