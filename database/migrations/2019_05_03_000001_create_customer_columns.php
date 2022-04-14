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
        /**
         * Commented due to this app using a different structure from 
         * Laravel Cashier's default(using User model). This app uses 
         * Company instead of User.
         */

        // Schema::table('users', function (Blueprint $table) {
        //     $table->string('stripe_id')->nullable()->index();
        //     $table->string('card_brand')->nullable();
        //     $table->string('card_last_four', 4)->nullable();
        //     $table->timestamp('trial_ends_at')->nullable();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('users', function (Blueprint $table) {
        //     $table->dropColumn([
        //         'stripe_id',
        //         'pm_type',
        //         'pm_last_four',
        //         'trial_ends_at',
        //     ]);
        // });
    }
};
