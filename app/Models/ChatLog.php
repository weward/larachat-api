<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatLog extends Model
{
    use HasFactory;

    protected $table = 'chat_logs';

    protected $guarded = [];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}
