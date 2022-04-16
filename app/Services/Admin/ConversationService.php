<?php namespace App\Services\Admin;

use App\Events\SendMessage;
use App\Models\ChatLog;
use App\Models\ChatMessage;
use App\Models\ChatQueue;
use Illuminate\Support\Facades\DB;

class ConversationService
{
    /**
     * Fetch inbox
     */
    public function initInbox($req)
    {
        try {
            $entities = ChatLog::with(['messages' => function ($q) {
                $q->with('chatLog');
            }])->where('user_id', $req->user()->id)
                ->where('flag', 1)
                ->orderBy('updated_at', 'DESC')->get();

            $chatLogs = $entities->map(function ($chatLog) {
                return collect([
                    'chat_log_id' => $chatLog->id,
                    'name' => $chatLog->email,
                    'unread' => false,
                ]);
            });

            // return all messages from different chat logs as one array
            $chatLogMessages = $entities->pluck('messages');
            $chatLogMessages = $chatLogMessages->all();
            $messageLog = [];
            foreach ($chatLogMessages as $messages) {
                foreach ($messages as $message) {
                    $push = [
                        'chat_log_id' => $message->chat_log_id,
                        'customer' => $message->chatLog->email,
                        'agent_id' => $message->chatLog->user_id,
                        'chat_message_id' => $message->id,
                        'message' => $message->message,
                        'from' => $message->from_flag,
                        'time' => strtotime($message->created_at)
                    ];
                    array_push($messageLog, $push);
                }
            }

            return [
                'chatLogs' => $chatLogs,
                'messages' => $messageLog,
            ];
        } catch (\Throwable $th) {
            return $th->getMessage();
            return false;
        }
    }

    /**
     * Add New Customer to inbox
     * ew Customer into Agent's Active Chat List
     * from queue.
     * 
     * @param object $req
     * @return array
     */
    public function addCustomer($req)
    {
        DB::beginTransaction();
        try {
            $chatQueue = ChatQueue::first();

            if (!$chatQueue) {
                return false;
            }

            ChatQueue::where('id', $chatQueue->id)->delete();

            $chatLog = ChatLog::where('chat_queue_id', $chatQueue->id)->first();
            $chatLog->user_id = $req->user()->id; // agent
            $chatLog->save();

            // send message to customer
            $message = "Good day! Thank you for waiting. How may I help you?"; // Get this from agent's settings
            $data = [
                'chat_log_id' => $chatLog->id,
                'customer' => $chatLog->email,
                'agent_id' => $chatLog->user_id,
                'chat_message_id' => 0,
                'message' => $message,
                'from' => 1,
                'time' => strtotime('now'),
            ];

            broadcast(new SendMessage($data))->toOthers();

            DB::commit();

            return [
                'chat_log_id' => $chatLog->id,
                'email' => $chatQueue->email
            ];
        } catch (\Throwable $th) {
            DB::rollBack();

            return false;
        }
    }

    /**
     * Message Sending
     * 
     * @param object $req
     * @return array
     */
    public function sendMessage($req)
    {
        DB::beginTransaction();
        try {
            $channel = explode('.', $req->channel_name);
            $chatLog = ChatLog::find($channel[1]);

            $chatMessage = ChatMessage::create([
                'chat_log_id' => $chatLog->id,
                'from_flag' => $req->from,
                'message' => $req->message,
            ]);

            // update chatlog
            $chatLog->touch();

            DB::commit();

            $data = [
                'chat_log_id' => $chatLog->id,
                'customer' => $chatLog->email,
                'agent_id' => $chatLog->user_id,
                'chat_message_id' => $chatMessage->id,
                'message' => $req->message,
                'from' => $req->from,
                'time' => strtotime($chatMessage->created_at),
            ];

            broadcast(new SendMessage($data))->toOthers();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            return false;
        }
    }
}