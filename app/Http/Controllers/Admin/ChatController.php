<?php

namespace App\Http\Controllers\Admin;

use App\Events\SendTest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function test()
    {
        event(new SendTest('This is a test message'));
    }

    
}
