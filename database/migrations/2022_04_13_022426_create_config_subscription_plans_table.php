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
        Schema::create('config_subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('free_user_quota')->default(1);
            $table->integer('free_chat_app_quota')->default(1);
            $table->decimal('price_per_additional_user');
            $table->decimal('price_per_additional_chat_app');
            $table->boolean('unlimited_chats')->default(1);
            $table->integer('lack_of_agent_response_timeout')->default(15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('config_subscription_plans');
    }
};
