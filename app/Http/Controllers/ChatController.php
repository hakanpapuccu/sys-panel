<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ChatController extends Controller
{
    /**
     * Display the chat interface
     */
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get();

        return view('chat.index', compact('users'));
    }

    /**
     * Get messages between current user and specified user
     */
    public function getMessages(User $user)
    {
        $messages = Message::where(function ($query) use ($user) {
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', Auth::id());
        })
        ->orderBy('created_at', 'asc')
        ->get();

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
    public function sendMessage(Request $request)
    {
        $isGeneral = $request->boolean('is_general');

        $request->validate([
            'receiver_id' => [
                Rule::requiredIf(! $isGeneral),
                'nullable',
                'integer',
                'exists:users,id',
                Rule::notIn([Auth::id()]),
            ],
            'message' => 'required|string|max:5000',
            'is_general' => 'nullable|boolean',
        ]);

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
        $conversations = User::whereHas('sentMessages', function ($query) use ($userId) {
            $query->where('receiver_id', $userId);
        })->orWhereHas('receivedMessages', function ($query) use ($userId) {
            $query->where('sender_id', $userId);
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
        ->get()
        ->map(function ($user) use ($userId) {
            $lastMessage = collect([
                $user->sentMessages->first(),
                $user->receivedMessages->first()
            ])->filter()->sortByDesc('created_at')->first();

            $unreadCount = Message::where('sender_id', $user->id)
                ->where('receiver_id', $userId)
                ->where('is_read', false)
                ->count();

            return [
                'user' => $user,
                'last_message' => $lastMessage,
                'unread_count' => $unreadCount,
            ];
        })
        ->sortByDesc('last_message.created_at')
        ->values();

        return response()->json($conversations);
    }

    /**
     * Get general/public messages
     */
    public function getGeneralMessages()
    {
        $messages = Message::where('is_general', true)
            ->with(['sender'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }
}
