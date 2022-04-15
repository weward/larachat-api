<?php

namespace App\Http\Controllers\Embed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Embed\QueueService;
use App\Http\Requests\QueueRequest;

class QueueController extends Controller
{
    protected $repo;

    public function __construct(QueueService $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Connect to Chat Queue
     */
    public function connect(QueueRequest $request)
    {
        if ($res = $this->repo->connect($request)) {
            return response()->json($res, 200);
        }

        return response()->json($res, 500);
    }
}
