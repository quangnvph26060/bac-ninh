<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageSent implements ShouldBroadcast
{

    public function __construct(public $message)
    {
    }

    public function broadcastOn()
    {
        $receiverType = class_basename($this->message->receiver_type); // Chỉ lấy "User" hoặc "Employee"
        $channelName = 'chat.' . $receiverType . '.' . $this->message->receiver_id;

        return new PrivateChannel($channelName);
    }



    public function broadcastWith()
    {
        $sender = null;
        if ($this->message->sender_type === 'App\Models\Employee') {
            $sender = \App\Models\Employee::find($this->message->sender_id);
            $avatar = $sender ? showImage($sender->avatar) : null;
        } elseif ($this->message->sender_type === 'App\Models\User') {
            $sender = \App\Models\User::find($this->message->sender_id);
            $avatar = $sender ? showImage($sender->img_url) : null;
        } else {
            $avatar = null;
        }

        return [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'sender_id' => $this->message->sender_id,
            'sender_type' => $this->message->sender_type,
            'receiver_id' => $this->message->receiver_id,
            'receiver_type' => $this->message->receiver_type,
            'created_at' => $this->message->created_at->format('d/m/Y H:i'),
            'avatar' => $avatar,
        ];
    }
}
