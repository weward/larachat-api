<?php

namespace App\Http\Controllers\Admin\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\MessageRequest;
use App\Services\Admin\ConversationService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    protected $repo;

    public function __construct(ConversationService $repo)
    {
        $this->repo = $repo;
    }

    public function send(MessageRequest $request)
    {
        if ($res = $this->repo->sendMessage($request)) {
            return response()->json($res, 200);
        }

        return response()->json('Failed', 500);
    }
}
