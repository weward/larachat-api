<?php namespace App\Services\Embed;

use App\Models\ChatApp;
use App\Models\ChatLog;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class RenderService 
{
    /**
     * Get App Settings
     *  Nuxt connects here to get app settins 
     *  for validated hash
     */
    public function embedAppSettings($req)
    {
        $isChatSessionActive = true;

        // load chat bot settings if has
        // $app = ChatApp::with(['chat_app_settings'])->where('hash', $req->hash)->first();
        $app = ChatApp::where('hash', $req->hash)->first();

        // Has active session, check if session is still valid
        if ($req->channel_name !== null) {
            $exp = explode('.', $req->channel_name);
            $chatLog = ChatLog::find($exp[1]);

            // closed session
            if ($chatLog->flag == 0) {
                $isChatSessionActive = false;
            }

            // If active for more than a day, shut it off
            if ($chatLog->created_at->copy()->diffInDays(now()) >= 1) {
                $isChatSessionActive = false;

                $chatLog->flag = 0;
                $chatLog->finished_at = now();
                $chatLog->save();
            }
        } else {
            $isChatSessionActive = false;
        }

        // Static Account for Chat Application Users
        // Dont Generate a new token! Old tokens will be useless!
        $chatUser = User::find(2);
        $accessToken = $chatUser->permanent_access_token;

        /** 
         * Generate unique random hash for each rendered chat app
         * This is for extra security so that DDoS via chat queue endpoint(Queue::connect()) would be prevented
         * Remember to check whether this random integer is present in the cache.
         * If it is present, allow the connection and forget the cached random integer.
         */
        $rand = rand(1000000, 9999999);
        $key = "rand_id_" . $rand;
        Cache::put($key, $rand, 3600);

        return [
            'response' => true,
            'app_settings' => $app,
            'rand_id' => $rand,
            'is_chat_session_active' => $isChatSessionActive,
            'access_token' => $accessToken,
        ];
    }
}