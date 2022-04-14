<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigSubscriptionPlan extends Model
{
    use HasFactory;

    protected $table = 'config_subscription_plans';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    
}
