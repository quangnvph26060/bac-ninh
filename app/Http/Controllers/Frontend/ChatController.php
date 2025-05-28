<?php

namespace App\Http\Controllers\Frontend;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function recentMessages(Request $request)
    {
        $user = auth('web')->user();
        $page = $request->input('page', 1);
        $limit = 20;

        $messages = Message::with(['sender', 'receiver'])
            ->where(function ($query) use ($user) {
                $query->where('sender_type', get_class($user))
                    ->where('sender_id', $user->id)
                    ->where('receiver_type', Employee::class);
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('receiver_type', get_class($user))
                    ->where('receiver_id', $user->id)
                    ->where('sender_type', Employee::class);
            })
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'is_read' => $msg->is_read,
                    'created_at' => $msg->created_at,
                    'sender' => $msg->sender instanceof Employee
                        ? [
                            'id' => $msg->sender->id,
                            'full_name' => showImage($msg->sender->full_name),
                            'avatar' => $msg->sender->avatar,
                        ]
                        : [
                            'id' => $msg->sender->id,
                            'name' => $msg->sender->name,
                            'img_url' => showImage($msg->sender->img_url),
                        ],
                    'receiver' => $msg->receiver instanceof Employee
                        ? [
                            'id' => $msg->receiver->id,
                            'full_name' => $msg->receiver->full_name,
                            'avatar' => showImage($msg->receiver->avatar),
                        ]
                        : [
                            'id' => $msg->receiver->id,
                            'name' => $msg->receiver->name,
                            'img_url' => showImage($msg->receiver->img_url),
                        ],
                ];
            })
            ->toArray();

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {

        $message = Message::create([
            'sender_type' => 'App\\Models\\User',
            'sender_id' => auth('web')->id(),
            'receiver_type' => 'App\\Models\\Employee',
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['status' => 'sent']);
    }

}
