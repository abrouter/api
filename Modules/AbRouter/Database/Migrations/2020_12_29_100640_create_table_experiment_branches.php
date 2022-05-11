<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableExperimentBranches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('experiment_branches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('experiment_id');
            $table->string('name', 120);
            $table->string('uid', 80);
            $table->integer('percent');
            $table->string('config', 5000);
            $table->timestamps();

            $table->index('experiment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('experiment_branches');
    }
}
