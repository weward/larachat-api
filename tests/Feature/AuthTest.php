<?php

namespace Tests\Feature;

use App\Jobs\SendVerificationEmail;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Login
     * 
     * @test
     */
    public function loggedInSuccessfully()
    {
        Company::factory()->create();
        $user = User::factory()->create([
            'password' => Hash::make('secret123')
        ]);

        info($user);

        $this->postJson(route('api.login', [
            'email' => $user->email,
            'password' => 'secret123'
        ]))->assertStatus(200);
    }

    /**
     * Login Failed Validation
     * 
     * @test
     */
    public function loginFailedValidation()
    {
        Company::factory()->create();
        $user = User::factory()->create([
            'password' => Hash::make('secret123')
        ]);
        $this->postJson(route('api.login', [
            'email' => $user->email,
            'password' => ''
        ]))->assertStatus(422);
    }

    /**
     * Login Failed
     * 
     * @test
     */
    public function loginFailed()
    {
        Company::factory()->create();
        $user = User::factory()->create([
            'password' => Hash::make('secret123')
        ]);
        $this->postJson(route('api.login', [
            'email' => $user->email,
            'password' => 'asdasdsadf'
        ]))->assertStatus(500);
    }

    /**
     * Sent Email Verification
     * 
     * @test
     */
    public function sentEmailVerification()
    {
        Bus::fake();

        Company::factory()->create();
        $user = User::factory()->create([
            'password' => Hash::make('secret123')
        ]);

        $this->getJson(route('api.resend-verification-email', ['id' => $user->id]));

        // Assert a job was pushed to a given queue...
        Bus::assertDispatched(SendVerificationEmail::class);
    }
}
