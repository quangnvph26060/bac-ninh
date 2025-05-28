<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{

    public function index()
    {
        $admin = auth('admin')->user();
        $search = request('search');

        $users = User::select('users.*', 'last_messages.message as last_message', 'last_messages.created_at as last_message_at', 'last_messages.is_read')
            ->leftJoin(DB::raw('
            (
                SELECT
                    IF(sender_type = "App\\\\Models\\\\User", sender_id, receiver_id) AS user_id,
                    MAX(id) as last_message_id
                FROM messages
                WHERE (sender_type = "App\\\\Models\\\\User" AND receiver_type = "App\\\\Models\\\\Employee")
                    OR (receiver_type = "App\\\\Models\\\\User" AND sender_type = "App\\\\Models\\\\Employee")
                GROUP BY user_id
            ) AS recent_messages
        '), 'users.id', '=', 'recent_messages.user_id')
            ->leftJoin('messages as last_messages', 'last_messages.id', '=', DB::raw('recent_messages.last_message_id'));

        if ($search) {
            $users->where('users.name', 'like', '%' . $search . '%');
        }

        $users = $users->orderByDesc('last_messages.created_at')->paginate(20);

        // Nếu là AJAX request, trả về partial view
        if (request()->ajax()) {
            return response()->json([
                'html' => view('admin.chat.contact-list', compact('users'))->render()
            ]);
        }

        return view('admin.chat.index', compact('users'));
    }


    public function loadMoreUsers(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search');

        $users = User::select('users.*', 'last_messages.message as last_message', 'last_messages.created_at as last_message_at', 'last_messages.is_read')
            ->leftJoin(DB::raw('
            (
                SELECT
                    IF(sender_type = "App\\\\Models\\\\User", sender_id, receiver_id) AS user_id,
                    MAX(id) as last_message_id
                FROM messages
                WHERE (sender_type = "App\\\\Models\\\\User" AND receiver_type = "App\\\\Models\\\\Employee")
                    OR (receiver_type = "App\\\\Models\\\\User" AND sender_type = "App\\\\Models\\\\Employee")
                GROUP BY user_id
            ) AS recent_messages
        '), 'users.id', '=', 'recent_messages.user_id')
            ->leftJoin('messages as last_messages', 'last_messages.id', '=', DB::raw('recent_messages.last_message_id'));

        if ($search) {
            $users->where('users.name', 'like', '%' . $search . '%');
        }

        $users = $users->orderByDesc('last_messages.created_at')
            ->paginate(10, ['*'], 'page', $page);

        $formattedUsers = $users->getCollection()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'img_url' => showImage($user->img_url),
                'last_message' => $user->last_message ?? 'Chưa có tin nhắn',
                'last_message_at' => $user->last_message_at ? \Carbon\Carbon::parse($user->last_message_at)->diffForHumans() : null,
            ];
        });

        return response()->json([
            'users' => $formattedUsers,
            'hasMorePages' => $users->hasMorePages(),
            'nextPage' => $users->currentPage() + 1,
        ]);
    }

    public function getMessages(Request $request, $userId)
    {
        $admin = auth('admin')->user();
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 20);
        $offset = ($page - 1) * $limit;

        $query = Message::with(['sender', 'receiver'])
            ->where(function ($query) use ($userId, $admin) {
                $query->where('sender_type', get_class($admin))
                    ->where('sender_id', $admin->id)
                    ->where('receiver_type', User::class)
                    ->where('receiver_id', $userId);
            })
            ->orWhere(function ($query) use ($userId, $admin) {
                $query->where('sender_type', User::class)
                    ->where('sender_id', $userId)
                    ->where('receiver_type', get_class($admin))
                    ->where('receiver_id', $admin->id);
            });

        $totalMessages = $query->count();

        $messages = $query->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->sortBy('created_at') // Sắp lại tăng dần
            ->values()
            ->map(function ($message) {
                $isAdminMessage = $message->sender_type === Employee::class;

                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'created_at' => $message->created_at,
                    'is_admin' => $isAdminMessage,
                    'from' => [
                        'id' => $message->sender->id ?? null,
                        'name' => $message->sender->name ?? $message->receiver->full_name,
                        'image' => showImage(optional($message->sender)->avatar ?? optional($message->sender)->img_url),
                    ],
                    'to' => [
                        'id' => $message->receiver->id ?? null,
                        'name' => $message->receiver->full_name ?? $message->sender->name,
                        'image' => showImage(optional($message->receiver)->avatar ?? optional($message->receiver)->img_url),
                    ],
                ];
            });

        return response()->json([
            'messages' => $messages,
            'has_more' => $totalMessages > $page * $limit,
        ]);
    }

    public function sendMessage(Request $request)
    {
        $sender = auth('admin')->check() ? auth('admin')->user() : auth('web')->user();

        $message = Message::create([
            'sender_type' =>  'App\\Models\\Employee',
            'sender_id' => $sender->id,
            'receiver_type' => 'App\\Models\\User',
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['status' => 'sent']);
    }
}
