<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SentEmailsLogs extends Migration
{
    public function up()
    {
        Schema::create('sent_emails_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);
            $table->bigInteger('proxy_binding_id')->index();
            $table->string('to', 300)->index();
            $table->string('from', 300)->index();
            $table->string('meta', 1000);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sent_emails_logs');
    }
}
