<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;

    protected $table = 'user_logs';

    /**
     * Set all fields as mass assignable
     */
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
