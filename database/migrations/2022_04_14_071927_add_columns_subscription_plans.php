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
        Schema::table('config_subscription_plans', function (Blueprint $table) {
            $table->after('name', function ($table) {
                $table->decimal('price', 11, 2)->default(0.00);
                $table->string('frequency')->default('monthly');
                $table->integer('trial_period')->default(7);
            });
            $table->after('lack_of_agent_response_timeout', function ($table) {
                $table->string('stripe_price_id')->nullable();
                $table->string('stripe_price_id_user')->nullable();
                $table->string('stripe_price_id_chat_app')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('config_subscription_plans', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('frequency');
            $table->dropColumn('trial_period');
            $table->dropColumn('stripe_price_id');
            $table->dropColumn('stripe_price_id_user');
            $table->dropColumn('stripe_price_id_chat_app');
        });
    }
};
