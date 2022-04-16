<?php

namespace App\Http\Controllers\Admin\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\ConversationService;

class CustomerController extends Controller
{
    protected $repo;

    public function __construct(ConversationService $repo)
    {
        $this->repo = $repo;
    }

    public function initInbox(Request $request)
    {
        if ($res = $this->repo->initInbox($request)) {
            return response()->json($res, 200);
        }

        return response()->json('Failed to load inbox.', 500);
    }

    public function add(Request $request)
    {
        if ($res = $this->repo->addCustomer($request)) {
            return response()->json($res, 200);
        }

        return response()->json('Action failed.', 500);
    }
}
