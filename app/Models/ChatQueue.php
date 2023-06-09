<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatQueue extends Model
{
    use HasFactory;

    protected $table = 'chat_queues';

    protected $guarded = [];
}
