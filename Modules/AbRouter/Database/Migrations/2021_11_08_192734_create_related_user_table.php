<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelatedUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('related_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('owner_id');
            $table->bigInteger('event_id')->nullable();
            $table->string('user_id');
            $table->string('related_user_id');

            $table->timestamps();

            $table->index('owner_id');
            $table->index('event_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('related_users');
    }
}
