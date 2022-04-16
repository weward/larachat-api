<?php namespace App\Services\Embed;

use App\Models\ChatApp;
use App\Models\ChatLog;
use App\Models\ChatQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class QueueService 
{
    public function connect($req)
    {
        DB::beginTransaction();
        try {
            /**
             * Check whether rand_id is in the cache
             * If it is not in the cache, it was not generated from this api and 
             * might be some form of attack.
             */
            if (!Cache::has("rand_id_" . $req->rand_id)) {
                return false;
            }

            /**
             * Get ID from cache or store it if it does not exist --using hash as the key
             */
            $chatApp = Cache::rememberForever("chat_app_hash_" . $req->chat_app_hash, function () use ($req) {
                return ChatApp::where('hash', $req->chat_app_hash)->first();
            });

            /**
             * Invalid hash
             */
            if (!$chatApp) {
                return false;
            }

            $chatQueue = ChatQueue::create([
                'email' => $req->email,
                'chat_app_id' => $chatApp->id,
            ]);

            $chatLogId = ChatLog::insertGetId([
                'email' => $chatQueue->email,
                'queued_at' => $chatQueue->created_at,
                'chat_queue_id' => $chatQueue->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            /**
             * Forget the unique random hash (rand_id)
             * so that there would be just one login/connection
             */
            Cache::forget("rand_id_" . $req->rand_id);

            DB::commit();

            return [
                'response' => true,
                'channel_name' => "chat.{$chatLogId}",
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            
            return false;
        }
    }
}