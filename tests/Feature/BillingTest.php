<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BillingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get Stripe
     * 
     * @test
     */
    public function initBillingIndexPage()
    {
        $company = Company::factory()->create();

        Sanctum::actingAs(
            $user = User::factory()->create([
                'company_id' => $company->id
            ]),
            []
        );

        $company->in_charge = $user->id;
        $company->save();

        $this->getJson(route('api.stripe.index'))->assertStatus(200);
    }

    /**
     * Setup Payment Method
     * 
     * @test
     */
    public function setupPaymentMethod()
    {
        $company = Company::factory()->create();

        Sanctum::actingAs(
            $user = User::factory()->create([
                'company_id' => $company->id
            ]),
            []
        );

        $company->in_charge = $user->id;
        $company->save();

        // Create a stripe customer
        $this->getJson(route('api.stripe.index'));

        // Setup payment method
        $res = $this->postJson(route('api.stripe.setup-payment-method'), [
            'payment_method' => 'pm_card_visa'
        ]);

        $res->assertStatus(200);
    }
}
