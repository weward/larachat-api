<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendMessage
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function broadcastAs()
    {
        return "SendMessage";
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("chat.{$this->data['chat_log_id']}");
    }

    public function broadcastWith()
    {
        return [
            'chat_log_id' => $this->data['chat_log_id'],
            'customer' => $this->data['customer'],
            'agent_id' => $this->data['agent_id'],
            'chat_message_id' => $this->data['chat_message_id'],
            'message' => $this->data['message'],
            'from' => $this->data['from'],
            'time' => $this->data['time'],
        ];
    }
}
