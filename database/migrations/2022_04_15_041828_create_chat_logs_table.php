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
                ->nullable()
                ->comment('agent')
                ->constrained();
            $table->bigInteger('chat_queue_id')->nullable();
            $table->dateTime('queued_at', 0)->comment('created_at of queue');
            $table->dateTime('finished_at', 0)->nullable();
            $table->tinyInteger('flag')->default(1)->comment('0 - Done, 1 - Active');            
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
