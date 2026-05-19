<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\LecturerScore;
use App\Models\XpLog;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendEmailReminderJob;
use App\Jobs\SendCalendarReminderJob;
use App\Jobs\SendWhatsAppReminderJob;
use App\Events\MessageSent;

class MessageController extends Controller
{
    /**
     * ============================================================
     * DELAY CONFIGURATION (untuk testing / production)
     * ============================================================
     * Ubah nilai di bawah ini untuk mengatur delay eskalasi:
     * 
     * TESTING (saat ini):
     *   - Email:    30 detik
     *   - Calendar: 1 menit
     *   - WhatsApp: 3 menit
     * 
     * PRODUCTION (ganti nanti):
     *   - Email:    now()->addMinutes(1)
     *   - Calendar: now()->addMinutes(2)
     *   - WhatsApp: now()->addDays(4)
     * ============================================================
     */
    private function getEmailDelay()
    {
        return now()->addSeconds(30);       // PRODUCTION: now()->addMinutes(1)
    }

    private function getCalendarDelay()
    {
        return now()->addMinutes(1);        // PRODUCTION: now()->addMinutes(2)
    }

    private function getWhatsAppDelay()
    {
        return now()->addMinutes(3);        // PRODUCTION: now()->addDays(4)
    }

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

        // ============================================================
        // ESKALASI BERTAHAP: Email → Calendar → WhatsApp
        // ============================================================
        if ($isStudent) {
            if ($conversation->type === 'private') {
                $lecturerMapping = $conversation->conversationUsers->where('user_id', '!=', $userId)->first();
                $lecturer = $lecturerMapping ? $lecturerMapping->user : null;

                if ($lecturer && $lecturer->role === 'dosen') {
                    // Simpan reminder record (tanpa langsung buat Calendar event)
                    $reminderService->trackReminder($lecturer, $user);

                    // TAHAP 1: Email setelah delay
                    SendEmailReminderJob::dispatch($message)
                        ->delay($this->getEmailDelay());

                    // TAHAP 2: Google Calendar setelah delay lebih lama
                    SendCalendarReminderJob::dispatch($message)
                        ->delay($this->getCalendarDelay());

                    // TAHAP 3: WhatsApp setelah delay paling lama
                    SendWhatsAppReminderJob::dispatch($message)
                        ->delay($this->getWhatsAppDelay());

                    Log::info("[Eskalasi] 3 job terjadwal untuk message ID {$message->id} ke dosen {$lecturer->name}");
                }
            } else {
                // Group Chat: Check for tag @namadosen
                $content = $request->input('content');
                foreach ($conversation->conversationUsers as $cu) {
                    $target = $cu->user;
                    if ($target && $target->role === 'dosen' && str_contains(strtolower($content), '@' . strtolower(str_replace(' ', '', $target->name)))) {
                        $reminderService->trackReminder($target, $user, $conversation->name);

                        // TAHAP 1: Email
                        SendEmailReminderJob::dispatch($message)
                            ->delay($this->getEmailDelay());

                        // TAHAP 2: Calendar
                        SendCalendarReminderJob::dispatch($message)
                            ->delay($this->getCalendarDelay());

                        // TAHAP 3: WhatsApp
                        SendWhatsAppReminderJob::dispatch($message)
                            ->delay($this->getWhatsAppDelay());

                        Log::info("[Eskalasi] 3 job terjadwal untuk message ID {$message->id} ke dosen {$target->name} (grup)");
                    }
                }
            }
        } else {
            // Dosen membalas → update/hapus data reminder internal saja
            if ($conversation->type === 'private') {
                $studentMapping = $conversation->conversationUsers->where('user_id', '!=', $userId)->first();
                $student = $studentMapping ? $studentMapping->user : null;
                if ($student) {
                    $reminderService->removeReminder($user, $student);
                }
            } else {
                // Dosen bicara di grup → clear reminder mereka
                $reminder = \App\Models\LecturerReminder::where('lecturer_id', $userId)->first();
                if ($reminder) {
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
