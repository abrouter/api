<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableDisplayUserEventsType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('display_user_events', function (Blueprint $table) {
            $table->string('type')->after('event_name')->default('incremental');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('display_user_events', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
