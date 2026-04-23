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
    public function store(Request $request, $conversationId, \App\Services\ReminderService $reminderService)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $conversation = Conversation::with('conversationUsers.user')->findOrFail($conversationId);
        $user = Auth::user();
        $userId = $user->id;
        
        // Security check
        if (!$conversation->conversationUsers->pluck('user_id')->contains($userId)) {
            abort(403);
        }

        // Logic 3x24h Cooldown (Only if student is sending to lecturer)
        $isStudent = $user->role === 'mahasiswa';
        if ($isStudent) {
            if ($conversation->type === 'private') {
                $hasLecturer = $conversation->conversationUsers->contains(fn($cu) => $cu->user && $cu->user->role === 'dosen');
                
                if ($hasLecturer) {
                    $lastMessage = Message::where('conversation_id', $conversationId)->latest()->first();
                    if ($lastMessage && $lastMessage->sender_id === $userId && $lastMessage->created_at > now()->subDays(3)) {
                        return response()->json([
                            'status' => 'error', 
                            'is_blocked' => true,
                            'message' => 'Harap tunggu balasan dosen (maksimal 3x24 jam) sebelum mengirim pesan baru.'
                        ], 422);
                    }
                }
            } else if ($conversation->type === 'group') {
                $contentLower = strtolower($request->input('content'));
                
                // Fetch recent messages efficiently in memory
                $recentMessages = Message::where('conversation_id', $conversationId)
                    ->where('sender_id', $userId)
                    ->where('created_at', '>', now()->subDays(3))
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($conversation->conversationUsers as $cu) {
                    $target = $cu->user;
                    if ($target && $target->role === 'dosen') {
                        $mentionTag = '@' . strtolower(str_replace(' ', '', $target->name));
                        
                        if (str_contains($contentLower, $mentionTag)) {
                            $lastMentionAt = null;
                            foreach ($recentMessages as $msg) {
                                if (str_contains(strtolower($msg->content), $mentionTag)) {
                                    $lastMentionAt = $msg->created_at;
                                    break;
                                }
                            }

                            if ($lastMentionAt) {
                                $lecturerReply = Message::where('conversation_id', $conversationId)
                                    ->where('sender_id', $target->id)
                                    ->where('created_at', '>', $lastMentionAt)
                                    ->first();

                                if (!$lecturerReply) {
                                    return response()->json([
                                        'status' => 'error', 
                                        'is_blocked' => true,
                                        'message' => 'Harap tunggu balasan 3x24 jam dari ' . $target->name . ' sebelum me-mention beliau kembali di grup ini.'
                                    ], 422);
                                }
                            }
                        }
                    }
                }
            }
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

        // Reward the sender
        LecturerScore::adjustXP($userId, 5, 'Mengirim pesan di Forum');

        // SMART SYNC & REMINDERS
        if ($isStudent) {
            // Student to Lecturer?
            if ($conversation->type === 'private') {
                $lecturerMapping = $conversation->conversationUsers->where('user_id', '!=', $userId)->first();
                $lecturer = $lecturerMapping ? $lecturerMapping->user : null;
                if ($lecturer && $lecturer->role === 'dosen') {
                    $reminderService->upsertReminder($lecturer, $user);
                    
                    // Auto-Email Notification
                    try {
                        $actionUrl = url('/forum/' . $conversation->id);
                        Mail::to($lecturer->email)->send(new NewMessageMail($user, $request->input('content'), 'Private Consultation', $actionUrl));
                    } catch (\Exception $e) {
                        \Log::error('Gagal mengirim email notifikasi ke dosen (Private): ' . $e->getMessage());
                    }
                }
            } else {
                // Group Chat: Check for tag @namadosen
                $content = $request->input('content');
                foreach ($conversation->conversationUsers as $cu) {
                    $target = $cu->user;
                    if ($target->role === 'dosen' && str_contains(strtolower($content), '@' . strtolower(str_replace(' ', '', $target->name)))) {
                        $reminderService->upsertReminder($target, $user, $conversation->name);
                        
                        // Auto-Email Notification
                        try {
                            $actionUrl = url('/forum/' . $conversation->id);
                            Mail::to($target->email)->send(new NewMessageMail($user, $content, $conversation->name, $actionUrl));
                        } catch (\Exception $e) {
                            \Log::error('Gagal mengirim email notifikasi ke dosen (Group): ' . $e->getMessage());
                        }
                    }
                }
            }
        } else {
            // Lecturer to Student(s)?
            // If lecturer replies, remove the reminder
            if ($conversation->type === 'private') {
                $studentMapping = $conversation->conversationUsers->where('user_id', '!=', $userId)->first();
                $student = $studentMapping ? $studentMapping->user : null;
                if ($student) {
                    $reminderService->removeReminder($user, $student);
                }
            } else {
                // In group, if lecturer speaks, they might be replying to everyone or specific ones.
                // Simple: when lecturer speaks in group, we clear their active reminder for that group's unreplied list?
                // The requirement doesn't specify group reply as clearly. 
                // Let's assume any message from lecturer clears their reminder for that conversation context.
                $reminder = \App\Models\LecturerReminder::where('lecturer_id', $userId)->first();
                if ($reminder) {
                    // Logic to find which students in this group were waiting
                    $studentsInGroup = $conversation->conversationUsers->pluck('user_id')->toArray();
                    $unreplied = $reminder->unreplied_students ?? [];
                    foreach ($unreplied as $sId) {
                        if (in_array($sId, $studentsInGroup)) {
                            $reminderService->removeReminder($user, User::find($sId));
                        }
                    }
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

        // Calculate if blocked after this message (Student to Lecturer)
        $isBlockedNow = false;
        if ($isStudent && $conversation->type === 'private') {
            $hasLecturer = $conversation->conversationUsers->contains(fn($cu) => $cu->user && $cu->user->role === 'dosen');
            if ($hasLecturer) {
                // Since student just sent a message, they are now blocked for 3x24h until reply
                $isBlockedNow = true;
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'Message sent!', 
                'message' => $message,
                'is_blocked' => $isBlockedNow
            ]);
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
