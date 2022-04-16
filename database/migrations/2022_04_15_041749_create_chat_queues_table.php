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
            $table->foreignId('chat_app_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('email');
            $table->timestamps();

            $table->index(['chat_app_id', 'email']);
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
