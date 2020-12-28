<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReceivedEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('received_emails', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);
            $table->longText('payload');
            $table->boolean('is_processed');
            $table->string('recipient_email', 500);
            $table->timestamps();

            $table->index(['recipient_email', 'is_processed']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('received_emails');
    }
}
