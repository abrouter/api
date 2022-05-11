<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableExperimentsStartExperimentDay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('experiments', function (Blueprint $table) {
            $table->timestamp('start_experiment_day')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('experiments', function (Blueprint $table) {
            $table->dropColumn('start_experiment_day');
        });
    }
}
