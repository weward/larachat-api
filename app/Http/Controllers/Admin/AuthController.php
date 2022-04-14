<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use App\Services\Admin\AuthService;

class AuthController extends Controller
{
    protected $repo;

    public function __construct(AuthService $repo)
    {
        $this->repo = $repo;
    }

    public function login(AuthRequest $request)
    {
        $res = $this->repo->login($request);
        if ($res['response']) {
            return response()->json($res, 200);
        }

        return response()->json([
            'message' => $res['message'] != '' ? $res['message'] : 'Failed to login',
            'user_id' => $res['user_id'] 
        ], 500);
    }

    public function logout(Request $request)
    {
        if ($this->repo->logout($request)) {
            return response()->json('You have been logged out of the system.', 200);
        }

        return response()->json('Failed to logout. Please try again', 500);
    }
}
