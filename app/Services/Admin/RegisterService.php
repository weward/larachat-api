<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\Company;
use App\Jobs\SendVerificationEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterService
{

    public function register($req)
    {
        DB::beginTransaction();
        try {
            $companyName = ($req->company == '') ? "No Company" : $req->company;

            $company = Company::create([
                'name' => $companyName,
                'subscription_plan_id' => 1
            ]);
            $user = User::create([
                'company_id' => $company->id,
                'name' => $req->name,
                'email' => $req->email,
                'password' => Hash::make($req->password)
            ]);

            $company->in_charge = $user->id;
            $company->save();

            // send verification email
            $details = [
                'url' => route('api.verify', ['id' => $user->id, 'hash' => sha1($user->id)]),
                'email' => $user->email
            ];

            SendVerificationEmail::dispatchIf(!is_null($user), $details);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            info($th->getMessage());
            return false;
        }

        return true;
    }

    public function verify($id)
    {
        DB::beginTransaction();
        try {
            $now = now();

            User::where('id', $id)->update([
                'email_verified_at' => $now,
            ]);

            Company::where('in_charge', $id)->update([
                'verified_at' => $now,
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return false;
        }

        return true;
    }

    /**
     * Resend Verification Email
     * 
     * @param  int $id User ID
     * @return boolean
     */
    public function resendVerificationEmail($id)
    {
        try {
            $user = User::find($id);

            // Resend verification email
            $details = [
                'url' => route('api.verify', ['id' => $user->id, 'hash' => sha1($user->id)]),
                'email' => $user->email
            ];

            SendVerificationEmail::dispatchIf(!is_null($user), $details);

            return true;
        } catch (\Throwable $th) {

            return false;
        }
    }

}
