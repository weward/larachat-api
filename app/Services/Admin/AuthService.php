<?php namespace App\Services\Admin;

use App\Models\User;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthService {

    public function login($req)
    {
        DB::beginTransaction();
        try {
            $user = User::where('email', $req->email)->first();

            /**
             * Check credentials
             * Or if manual password checking: 
             *  Hash::check('input', 'passwordFromDB')
             */
            if (!$res = Auth::attempt(['email' => $req->email, 'password' => $req->password])) {
                return ['response' => false, 'message' => "{$res}"];
            }

            if (is_null($user->email_verified_at)) {
                $resendRoute = route('api.resend-verification-email', ['id' => $user->id]);
                return [
                    'response' => false,
                    'message' => "Your account is not yet verified. Please check your email. <a href='{$resendRoute}'>Resend Verification Email.</a>"
                ];
            }

            // Log User Login
            $user->logs()->save(new UserLog([
                'user_id' => $user->id,
                'action' => 'Logged In'
            ]));

            DB::commit();

            return [
                'response' => true,
                'user' => $user->toJson(),
                'subscription_plan' => config("subscriptionplans.{$user->company->subscription_plan_id}")
            ];
        } catch (\Throwable $th) {
            DB::rollBack();

            return ['response' => false, 'message' => $th->getMessage()];
        }
    }
}