<?php namespace App\Services\Embed;

use App\Models\ChatApp;
use Illuminate\Support\Facades\Cache;

class Renderservice 
{
    /**
     * Get App Settings
     *  Nuxt connects here to get app settins 
     *  for validated hash
     */
    public function embedAppSettings($req)
    {
        // load chat bot settings if has
        // $app = ChatApp::with(['chat_app_settings'])->where('hash', $req->hash)->first();
        $app = ChatApp::where('hash', $req->hash)->first();

        /** 
         * Generate unique random hash for each rendered chat app
         * This is for extra security so that DDoS via chat queue endpoint(Queue::connect) would be prevented
         * Remember to check whether this random integer is present in the cache.
         * If it is present, allow the connection and forget the cached random integer.
         */
        $rand = rand(1000000, 9999999);
        Cache::put($rand, $rand, 3600);

        return [
            'response' => true,
            'app_settings' => $app,
            'rand_id' => $rand,
        ];
    }
}