<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProxyBindings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);
        Schema::create('proxy_bindings', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);
            $table->bigInteger('user_id');
            $table->bigInteger('reverse_for')->default(0);
            $table->string('proxy_address', 190);
            $table->timestamps();

            $table->unique(['proxy_address', 'reverse_for']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proxy_bindings');
    }
}
