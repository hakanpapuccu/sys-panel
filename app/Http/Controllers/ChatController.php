<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMessageRequest;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Display the chat interface
     */
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get(['id', 'name', 'profile_image']);

        return view('chat.index', compact('users'));
    }

    /**
     * Get messages between current user and specified user
     */
    public function getMessages(Request $request, User $user)
    {
        $sinceId = (int) $request->query('since_id', 0);

        $baseQuery = Message::query()->where(function ($query) use ($user) {
            $query->where(function ($subQuery) use ($user) {
                $subQuery->where('sender_id', Auth::id())
                    ->where('receiver_id', $user->id);
            })->orWhere(function ($subQuery) use ($user) {
                $subQuery->where('sender_id', $user->id)
                    ->where('receiver_id', Auth::id());
            });
        });

        if ($sinceId > 0) {
            $messages = (clone $baseQuery)
                ->where('id', '>', $sinceId)
                ->orderBy('id', 'asc')
                ->get();
        } else {
            $messages = (clone $baseQuery)
                ->orderBy('id', 'desc')
                ->limit(200)
                ->get()
                ->reverse()
                ->values();
        }

        // Mark messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json($messages);
    }

    /**
     * Send a new message
     */
    public function sendMessage(SendMessageRequest $request)
    {
        $isGeneral = $request->boolean('is_general');

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $isGeneral ? null : (int) $request->receiver_id,
            'message' => $request->message,
            'is_general' => $isGeneral,
        ]);

        // Send notification
        $message->load('sender');
        if ($isGeneral) {
            // Notify all other users
            $users = User::where('id', '!=', Auth::id())->get();
            foreach ($users as $user) {
                $user->notify(new \App\Notifications\MessageReceived($message));
            }
        } else {
            // Notify receiver
            $receiver = User::find($request->receiver_id);
            if ($receiver) {
                $receiver->notify(new \App\Notifications\MessageReceived($message));
            }
        }

        return response()->json([
            'success' => true,
            'message' => $message->load('sender', 'receiver'),
        ]);
    }

    /**
     * Get list of conversations (users the current user has chatted with)
     */
    public function getConversations()
    {
        $userId = Auth::id();

        // Get all users that the current user has messaged or received messages from
        $conversations = User::query()
            ->where('id', '!=', $userId)
            ->where(function ($query) use ($userId) {
                $query->whereHas('sentMessages', function ($subQuery) use ($userId) {
                    $subQuery->where('receiver_id', $userId);
                })->orWhereHas('receivedMessages', function ($subQuery) use ($userId) {
                    $subQuery->where('sender_id', $userId);
                });
            })
            ->with(['sentMessages' => function ($query) use ($userId) {
                $query->where('receiver_id', $userId)
                    ->latest()
                    ->limit(1);
            }, 'receivedMessages' => function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->latest()
                    ->limit(1);
            }])
            ->get(['users.id', 'users.name', 'users.profile_image']);

        $unreadCounts = Message::query()
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->whereIn('sender_id', $conversations->pluck('id'))
            ->selectRaw('sender_id, COUNT(*) as unread_count')
            ->groupBy('sender_id')
            ->pluck('unread_count', 'sender_id');

        $conversations = $conversations
            ->map(function ($user) use ($unreadCounts) {
                $lastMessage = collect([
                    $user->sentMessages->first(),
                    $user->receivedMessages->first(),
                ])->filter()->sortByDesc('created_at')->first();

                return [
                    'user' => $user,
                    'last_message' => $lastMessage,
                    'unread_count' => (int) ($unreadCounts[$user->id] ?? 0),
                ];
            })
            ->sortByDesc('last_message.created_at')
            ->values();

        return response()->json($conversations);
    }

    /**
     * Get general/public messages
     */
    public function getGeneralMessages(Request $request)
    {
        $sinceId = (int) $request->query('since_id', 0);

        $baseQuery = Message::query()
            ->where('is_general', true)
            ->with(['sender:id,name']);

        if ($sinceId > 0) {
            $messages = (clone $baseQuery)
                ->where('id', '>', $sinceId)
                ->orderBy('id', 'asc')
                ->get();
        } else {
            $messages = (clone $baseQuery)
                ->orderBy('id', 'desc')
                ->limit(200)
                ->get()
                ->reverse()
                ->values();
        }

        return response()->json($messages);
    }
}
