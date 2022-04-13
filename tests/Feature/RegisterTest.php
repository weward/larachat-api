<?php

namespace Tests\Feature;

use App\Jobs\SendVerificationEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Successful Registration
     * 
     * @test
     */
    public function registeredSuccessfully()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'test test',
            'company' => '',
            'email' => 'test@test.com',
            'password' => 'secret123',
            'confirm_password' => 'secret123',
        ]);

        $response->assertStatus(200);
    }

    /**
     * Email Verification Was Sent
     * 
     * @test
     */
    public function EmailVerificationSent()
    {
        Bus::fake();

        $this->postJson('/api/register', [
            'name' => 'test test',
            'company' => '',
            'email' => 'test@test.com',
            'password' => 'secret123',
            'confirm_password' => 'secret123',
        ]);

        // Assert a job was pushed to a given queue...
        Bus::assertDispatched(SendVerificationEmail::class);
    }

    /**
     * Email Was Verified
     * 
     * @test
     */
    public function emailVerified()
    {
        $user = User::factory()->create();
        $hashed = sha1($user->id);

        $this->getJson("/api/verify/{$user->id}/{$hashed}")
            ->assertRedirect(env('FRONTEND_APP_URL') . '/login');
    }

    /**
     * Registration Failed
     * 
     * @test
     */
    public function registrationFailed()
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'company' => '',
            'email' => '',
            'password' => 'asd',
            'confirm_password' => 'secret123',
        ]);

        $response->assertStatus(422);
    }

}
