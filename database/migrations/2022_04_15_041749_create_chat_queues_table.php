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
        Schema::create('chat_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->comment('agent')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('chat_app_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('email');
            $table->tinyInteger('flag')->default(0)->comment('0 - queued, 1 - active');
            $table->timestamps();

            $table->index(['user_id', 'chat_app_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_queues');
    }
};
