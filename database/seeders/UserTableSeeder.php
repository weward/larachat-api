<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = Company::create([
            'name' => 'Default',
            'subscription_plan_id' => 1,
        ]);

        $user = User::create([
            'company_id' => $company->id,
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('secret123'),
            'email_verified_at' => now()
        ]);

        $publicChatUser = User::create([
            'company_id' => $company->id,
            'name' => 'Public Chat User',
            'email' => 'public.chat.user@admin.com',
            'password' => Hash::make('secret123'),
            'email_verified_at' => now()
        ]);

        $publicChatUser->permanent_access_token = $publicChatUser->createToken('sanctum')->plainTextToken;
        $publicChatUser->save();

        $company->in_charge = $user->id;
        $company->save();
    }
}
