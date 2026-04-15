<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\LecturerScore;
use App\Models\XpLog;
use App\Mail\NewMessageMail;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Jobs\CheckMessageReplyJob;
use App\Events\MessageSent;

class MessageController extends Controller
{
    public function store(Request $request, $conversationId)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $conversation = Conversation::with('conversationUsers.user')->findOrFail($conversationId);
        $userId = Auth::id();
        
        // Security check
        if (!$conversation->conversationUsers->pluck('user_id')->contains($userId)) {
            abort(403);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
            'content' => $request->input('content'),
            'is_read' => false,
        ]);

        $conversation->update([
            'last_message_at' => now(),
        ]);

        // Dispatch Google Calendar & WA reminder check (1 minute delay)
        CheckMessageReplyJob::dispatch($message)->delay(now()->addMinute());

        // Reward the sender for replying
        LecturerScore::adjustXP($userId, 5, 'Membalas pesan di Forum');

        // Notification & Penalty Logic (ONLY for Private/DM)
        \Illuminate\Support\Facades\Log::info('[EMAIL DEBUG] conv type: ' . $conversation->type . ' | conv id: ' . $conversation->id);
        if ($conversation->type === 'private') {
            $recipientMapping = $conversation->conversationUsers->where('user_id', '!=', $userId)->first();
            $recipient = $recipientMapping ? $recipientMapping->user : null;
            \Illuminate\Support\Facades\Log::info('[EMAIL DEBUG] recipient: ' . ($recipient ? $recipient->email : 'NULL'));
            
            if ($recipient) {
                $unreadCount = Message::where('conversation_id', $conversationId)
                    ->where('sender_id', $userId)
                    ->where('is_read', false)
                    ->count();
                \Illuminate\Support\Facades\Log::info('[EMAIL DEBUG] unreadCount: ' . $unreadCount);

                if ($unreadCount < 3) {
                    try {
                        \Illuminate\Support\Facades\Log::info('[EMAIL DEBUG] dispatching Mail::later() to ' . $recipient->email);
                        Mail::to($recipient->email)->later(
                            now()->addMinute(),
                            new NewMessageMail(Auth::user(), $message->content)
                        );
                        \Illuminate\Support\Facades\Log::info('[EMAIL DEBUG] Mail::later() dispatched successfully. Jobs in table: ' . \Illuminate\Support\Facades\DB::table('jobs')->count());
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('[EMAIL DEBUG] Email dispatch failed: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
                    }
                } elseif ($unreadCount == 3) {
                    $waService = new WhatsAppService();
                    $waMsg = "Halo {$recipient->name}, ada pesan baru dari " . Auth::user()->name . " di EduForum yang belum Anda baca. Silakan balas segera!";
                    $waService->sendMessage($recipient->phone, $waMsg);

                    LecturerScore::adjustXP($recipient->id, -5, 'Penalti: 3 pesan belum dibalas');
                }
            }
        }

        // Trigger modal only once per session
        if (!session()->has('points_notified')) {
            session(['points_notified' => true]);
            session()->flash('showXpModal', true);
        }

        $message->load('sender');
        broadcast(new MessageSent($message))->toOthers();

        if ($request->expectsJson()) {
            return response()->json(['status' => 'Message sent!', 'message' => $message]);
        }

        return back()->with('status', 'Message sent!');
    }

    public function markAsRead($conversationId)
    {
        $userId = Auth::id();
        
        $unreadQuery = Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false);

        if ($unreadQuery->exists()) {
            $unreadQuery->update([
                'is_read' => true,
                'read_at' => now()
            ]);

            // Reward the reader for reading messages
            LecturerScore::adjustXP($userId, 5, 'Membaca pesan di Forum');
        }

        return response()->json(['status' => 'success']);
    }
}
