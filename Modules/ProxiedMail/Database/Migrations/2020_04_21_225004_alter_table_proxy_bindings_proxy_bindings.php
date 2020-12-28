<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProxyBindingsProxyBindings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proxy_bindings', function (Blueprint $table) {
            $table->integer('received_emails', false, true)->after('proxy_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proxy_bindings', function (Blueprint $table) {
            $table->dropColumn('received_emails');
        });
    }
}
