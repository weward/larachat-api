<?php namespace App\Services\Admin;

use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BillingService {
    /**
     * Get Stripe Customer
     * Attach Payment Method if has already setup one.
     * 
     */
    public function index($req)
    {
        DB::beginTransaction();
        try {
            $company = Company::with(['inCharge'])->where('id', $req->user()->company_id)->first();
            $stripeCustomer = $company->createOrGetStripeCustomer([
                'email' => $company->inCharge->email,
                'description' => $company->name
            ]);

            DB::commit();

            $company->trial_ends_at_trans = ($company->subscribed('default')
            && $company->subscription('default')->onTrial()
                && $company->trial_ends_at !== '')
                ? date('F d, y', strtotime($company->trial_ends_at))
                : '';

            return [
                'company' => $company,
                'stripe_customer' => $stripeCustomer,
                'intent' => $company->createSetupIntent()
            ];
        } catch (\Throwable $th) {
            DB::rollBack();

            return false;
        }
    }

    /**
     * Setup Payment Method & subscribe
     * 
     * @param  object $req
     * @return boolean
     */
    public function setupPaymentMethod($req)
    {
        DB::beginTransaction();
        try {
            $company = Company::find($req->user()->company_id);
            if ($company->hasDefaultPaymentMethod()) {
                $company->deletePaymentMethods(); // delete old
                $company->updateDefaultPaymentMethod($req->payment_method);
            } else {
                // $company->addPaymentMethod($req->payment_method);
                $company->updateDefaultPaymentMethod($req->payment_method);
            }

            // If has not yet subscribed
            if (!$company->subscribed('default')) {
                $this->subscribe($company, $req);
            }

            DB::commit();

            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th->getMessage();
            return false;
        }
    }

    /**
     * Subscribe to Default Plan
     * First Time Subscription
     */
    public function subscribe($company, $req)
    {
        try {
            // compute remaining trial days
            $daysPassedSinceVerification = now()->diffInDays($company->verified_at);
            $trialPeriodDays = config("subscriptionplans.{$company->subscription_plan_id}.trial_period");
            $trialDaysRemaining = $trialPeriodDays - $daysPassedSinceVerification;

            if ($trialPeriodDays > $daysPassedSinceVerification) {
                $company->newSubscription('default', config("subscriptionplans.{$company->subscription_plan_id}.stripe_price_id")) // default chat plan
                ->trialDays($trialDaysRemaining)
                    ->create($req->payment_method);
            } else {
                $company->newSubscription('default', config("subscriptionplans.{$company->subscription_plan_id}.stripe_price_id")) // default chat plan
                ->create($req->payment_method);
            }

            // setup trial_ends_at column value in companies table
            $verifiedDate = Carbon::createFromFormat('Y-m-d H:i:s', $company->verified_at);
            $company->trial_ends_at = $verifiedDate->addDays($trialPeriodDays);
            $company->save();
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }

    /**
     * Get Subscribed Stripe Customer
     * 
     * @param  object $company
     * @return object
     */
    public function getSubscribedStripeCustomer($company)
    {
        $stripeCustomer = Cashier::findBillable($company->stripe_id);
        $stripeCustomer->payment_method = $company->defaultPaymentMethod();

        return $stripeCustomer;
    }

    /**
     * Add New Plan And/Or Increment Plan's Quantity
     * 
     * Subscribe to User Plan, Chat Plan
     * Increment User Qty, Chat Qty
     * 
     * @param  object   $company    
     * @param  string   $entity     Refers to 'user', 'chatapp'
     * @param  int      $qty        The increment qty
     */
    public function addToPlan($company, $entity, $qty)
    {
        try {
            $plan = ($entity == 'user')
            ? config("subscriptionplans.{$company->subscription_plan_id}.stripe_price_id_user")
            : config("subscriptionplans.{$company->subscription_plan_id}.stripe_price_id_chat_app");

            if ($company->subscribedToPlan($plan, 'default')) {
                $company->subscription('default')->incrementQuantity($qty, $plan);
            } else {
                $company->subscription('default')->addPlan($plan, $qty);
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }

    /**
     * Decrement Quantity 
     *  (side-effect: remove additional plan)
     * 
     *  Decrement User Qty, Chat Qty
     *  Remove User Plan, Chat Plan
     * 
     * @param  object   $company    
     * @param  string   $entity     Refers to 'user', 'chatapp'
     * @param  int      $qty        The increment qty
     */
    public function decrementPlanQuantity($company, $entity, $qty)
    {
        try {
            $systemQty = ($entity == 'user')
            ? User::where('company_id', $company->id)->count()
                : ChatApp::where('company_id', $company->id)->count();

            $freeQty = ($entity == 'user')
            ? config("subscriptionplans.{$company->subscription_plan_id}.free_user_quota")
            : config("subscriptionplans.{$company->subscription_plan_id}.free_chat_app_quota");

            $plan = ($entity == 'user')
            ? config("subscriptionplans.{$company->subscription_plan_id}.stripe_price_id_user")
            : config("subscriptionplans.{$company->subscription_plan_id}.stripe_price_id_chat_app");

            if ($systemQty <= $freeQty) {
                // unsubscribe from plan
                $company->subscription('default')->removePlan($plan);
            } else {
                // decrement
                $company->subscription('default')->decrementQuantity($qty, $plan);
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }
}
