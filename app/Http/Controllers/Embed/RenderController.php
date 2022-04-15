<?php

namespace App\Http\Controllers\Embed;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmbedAppSettingsRequest;
use App\Services\Embed\Renderservice;
use Illuminate\Http\Request;

class RenderController extends Controller
{
    protected $repo;

    public function __construct(Renderservice $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Load Embedded Chat Settings
     */
    public function embedAppSettings(EmbedAppSettingsRequest $request)
    {
        $res = $this->repo->embedAppSettings($request);
        if ($res['response']) {
            return response()->json($res, 200);
        }

        return response()->json($res['message'], 500);
    }
}

