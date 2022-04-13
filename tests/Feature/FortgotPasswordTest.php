<?php

namespace Tests\Feature;

use App\Jobs\SendForgotPasswordEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FortgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Send User An Email Upon Request
     * 
     * @test
     */
    public function sentUserAResetPasswordEmail()
    {
        Bus::fake();

        $user = User::factory()->create();

        $this->postJson(route('api.forgot-password'), [
            'email' => $user->email
        ]);

        Bus::assertDispatched(SendForgotPasswordEmail::class);
    }

    /**
     * Verify Reset Password Link From Email
     * 
     * @test
     */
    public function verifiedResetPasswordLinkFromEmail()
    {
        $user = User::factory()->create();
        $hash = sha1(rand(100, 999));

        DB::table('reset_passwords')->insert([
            'user_id' => $user->id,
            'hash' => $hash,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString()
        ]);

        $this->getJson(route('api.reset-password', ['id' => $user->id, 'hash' => $hash]))
            ->assertRedirect(env('FRONTEND_APP_URL') . "/change-password/{$user->id}/{$hash}");
    }

}
