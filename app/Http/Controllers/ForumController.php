<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Conversation;
use App\Models\User;
use App\Models\ConversationUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class ForumController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $conversationIds = $user->userConversations()->pluck('conversation_id');
        
        $conversations = Conversation::whereIn('id', $conversationIds)
            ->with(['conversationUsers.user']) 
            ->withCount(['messages as unread_count' => function (Builder $query) use ($user) {
                $query->where('sender_id', '!=', $user->id)->where('is_read', false);
            }])
            ->orderByRaw('COALESCE(last_message_at, conversations.created_at) DESC')
            ->get();

        $chattedUserIds = ConversationUser::whereIn('conversation_id', $conversationIds)
            ->where('user_id', '!=', $user->id)
            ->pluck('user_id')
            ->unique();

        $allUsers = User::whereIn('id', $chattedUserIds)->get();

        return view('forum.index', compact('conversations', 'allUsers'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $users = [];

        if ($query) {
            $users = User::where('id', '!=', Auth::id())
                ->where('name', 'LIKE', '%' . $query . '%')
                ->get();
        }

        return view('forum.search', compact('users', 'query'));
    }
}
