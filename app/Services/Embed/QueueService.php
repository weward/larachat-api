<?php namespace App\Services\Embed;

use App\Models\ChatApp;
use App\Models\ChatQueue;
use Illuminate\Support\Facades\Cache;

class QueueService 
{
    public function connect($req)
    {
        try {
            /**
             * Check whether rand_id is in the cache
             * If it is not in the cache, it was not generated from this api and 
             * might be some form of attack.
             */
            if (!Cache::has($req->rand_id)) {
                return false;
            }

            /**
             * Get ID from cache or store it if it does not exist --using hash as the key
             */
            $chatAppId = Cache::rememberForever($req->chat_app_hash, function () use ($req) {
                return ChatApp::where('hash', $req->chat_app_hash)->first()->id;
            });

            /**
             * Invalid hash
             */
            if (!$chatAppId) {
                return false;
            }

            $chatQueue = ChatQueue::create([
                'email' => $req->email,
                'flag' => 0,
                'chat_app_id' => $chatAppId
            ]);

            /**
             * Forget the unique random hash (rand_id)
             * so that there would be just one login/connection
             */
            Cache::forget($req->rand_id);

            return [
                'response' => true,
                'channel_name' => "chat.{$chatQueue->id}",
            ];
        } catch (\Throwable $th) {
            return $th->getMessage();
            return false;
        }
    }
}