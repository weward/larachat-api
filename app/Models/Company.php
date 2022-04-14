<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;

class Company extends Model
{
    use HasFactory, Billable;
    
    protected $table = 'companies';

    /**
     * Set all fields as mass assignable
     */
    protected $guarded = [];

    public function inCharge()
    {
        return $this->belongsTo(User::class, 'in_charge');
    }
}
