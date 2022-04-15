<?php

namespace App\Http\Controllers\Embed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RenderController extends Controller
{
    public function embedAppSettings(EmbedAppSettingsService $request)
    {
        $res = $this->repo->embedAppSettings($request);
        if ($res['response']) {
            return response()->json($res['app_settings'], 200);
        }

        return response()->json($res['message'], 500);
    }
}

