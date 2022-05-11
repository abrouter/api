<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExperimentUserBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('experiment_user_branches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('experiment_user_id');
            $table->bigInteger('experiment_id');
            $table->bigInteger('experiment_branch_id');
            $table->timestamps();

            $table->index('experiment_user_id');
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
        Schema::dropIfExists('experiment_user_branches');
    }
}
