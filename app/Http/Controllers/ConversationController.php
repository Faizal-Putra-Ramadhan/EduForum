<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\ConversationUser;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $currentUserId = Auth::id();
        $otherUserId = $request->user_id;

        if ($currentUserId == $otherUserId) {
            return back()->withErrors('Cannot start conversation with yourself.');
        }

        // Find existing private conversation manually across drivers
        $convIds1 = ConversationUser::where('user_id', $currentUserId)->pluck('conversation_id');
        $convIds2 = ConversationUser::where('user_id', $otherUserId)->pluck('conversation_id');
        $commonIds = $convIds1->intersect($convIds2);

        $conversation = Conversation::whereIn('id', $commonIds)
            ->where('type', 'private')
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'type' => 'private',
                'last_message_at' => now(),
            ]);
            
            ConversationUser::create(['conversation_id' => $conversation->id, 'user_id' => $currentUserId, 'role' => 'member']);
            ConversationUser::create(['conversation_id' => $conversation->id, 'user_id' => $otherUserId, 'role' => 'member']);
        }

        return redirect('/forum/' . $conversation->id);
    }

    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);

        $conversation = Conversation::create([
            'name' => $request->name,
            'type' => 'group',
            'creator_id' => Auth::id(),
            'last_message_at' => now(),
        ]);

        $userIds = array_unique(array_merge($request->user_ids, [Auth::id()]));
        
        foreach ($userIds as $uid) {
            ConversationUser::create([
                'conversation_id' => $conversation->id,
                'user_id' => $uid,
                'role' => $uid == Auth::id() ? 'admin' : 'member'
            ]);
        }

        return redirect('/forum/' . $conversation->id)->with('status', 'Grup berhasil dibuat!');
    }

    public function show($id)
    {
        $user = Auth::user();
        $conversation = Conversation::with(['conversationUsers.user', 'messages.sender'])->findOrFail($id);

        if (!$conversation->conversationUsers->pluck('user_id')->contains($user->id)) {
            abort(403);
        }

        // Mark messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        $conversationIds = $user->userConversations()->pluck('conversation_id');
        $conversations = Conversation::whereIn('id', $conversationIds)
            ->with(['conversationUsers.user'])
            ->withCount(['messages as unread_count' => function ($query) use ($user) {
                $query->where('sender_id', '!=', $user->id)->where('is_read', false);
            }])
            ->orderByRaw('COALESCE(last_message_at, conversations.created_at) DESC')
            ->get();

        $showXpModal = false;
        if (!session()->has('points_notified')) {
            session(['points_notified' => true]);
            $showXpModal = true;
        }

        $chattedUserIds = ConversationUser::whereIn('conversation_id', $conversationIds)
            ->where('user_id', '!=', $user->id)
            ->pluck('user_id')
            ->unique();

        $allUsers = User::whereIn('id', $chattedUserIds)->get();

        // Calculate Blocked Status (Only if student is in a private chat with a lecturer)
        $isBlocked = false;
        if ($user->role === 'mahasiswa' && $conversation->type === 'private') {
            $hasLecturer = $conversation->conversationUsers->contains(function ($cu) {
                return $cu->user && $cu->user->role === 'dosen';
            });

            if ($hasLecturer) {
                $lastMsg = $conversation->messages->last();
                if ($lastMsg && $lastMsg->sender_id === $user->id && $lastMsg->created_at > now()->subDays(3)) {
                    $isBlocked = true;
                }
            }
        }

        return view('forum.conversation', compact('conversation', 'conversations', 'showXpModal', 'allUsers', 'isBlocked'));
    }
}
