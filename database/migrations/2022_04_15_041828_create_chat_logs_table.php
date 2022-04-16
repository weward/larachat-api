<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_logs', function (Blueprint $table) {
            $table->id();
            $table->string('email')->comment('customer');
            $table->foreignId('user_id')
                ->comment('agent')
                ->constrained();
            $table->foreignId('chat_queue_id')
                ->constrained();
            $table->dateTime('finished_at', 0);
            $table->timestamps();

            $table->index(['email', 'user_id', 'chat_queue_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_logs');
    }
};
