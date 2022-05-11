<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('owner_id');
            $table->string('temporary_user_id');
            $table->string('user_id');
            $table->string('event');
            $table->string('tag');
            $table->string('referrer');
            $table->string('meta', 1500);
            $table->string('ip', 50);
            
            $table->timestamps();
            
            $table->index(['owner_id', 'temporary_user_id']);
            $table->index('user_id');
            $table->index('event');
            $table->index('tag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
