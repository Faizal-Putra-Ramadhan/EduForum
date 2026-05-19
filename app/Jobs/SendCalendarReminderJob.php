<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;
use App\Models\LecturerReminder;
use App\Services\GoogleCalendarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendCalendarReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;

    /**
     * Create a new job instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     * 
     * TAHAP 2: Buat Google Calendar event multi-hari (3 hari ke depan)
     * jika dosen belum membalas setelah delay.
     */
    public function handle(GoogleCalendarService $googleService): void
    {
        // Cek apakah dosen sudah membalas
        $hasReply = Message::where('conversation_id', $this->message->conversation_id)
            ->where('sender_id', '!=', $this->message->sender_id)
            ->where('created_at', '>', $this->message->created_at)
            ->exists();

        if ($hasReply) {
            Log::info("[Eskalasi Tahap 2] Message ID {$this->message->id} sudah dibalas. Skip calendar reminder.");
            return;
        }

        // Cari dosen (recipient)
        $conversation = Conversation::with('conversationUsers.user')->find($this->message->conversation_id);
        if (!$conversation) return;

        $recipientMapping = $conversation->conversationUsers->filter(function ($cu) {
            return (string)$cu->user_id !== (string)$this->message->sender_id;
        })->first();

        $recipient = $recipientMapping ? $recipientMapping->user : null;

        if (!$recipient) {
            Log::warning("[Eskalasi Tahap 2] Tidak ada recipient untuk message ID {$this->message->id}");
            return;
        }

        if (!$recipient->google_token) {
            Log::info("[Eskalasi Tahap 2] Dosen {$recipient->name} belum connect Google. Skip calendar.");
            return;
        }

        Log::info("[Eskalasi Tahap 2] Membuat Google Calendar reminder untuk dosen {$recipient->name} (message ID {$this->message->id})");

        // Buat event multi-hari: mulai besok, 3 hari
        $sender = $this->message->sender;
        $startDate = Carbon::tomorrow(); // besok
        $endDate = Carbon::tomorrow()->addDays(3); // 3 hari setelah besok

        $studentNames = [$sender->name];

        // Cek apakah sudah ada reminder untuk dosen ini
        $reminder = LecturerReminder::firstOrCreate(
            ['lecturer_id' => $recipient->id],
            ['unreplied_students' => [], 'group_sources' => []]
        );

        $students = $reminder->unreplied_students ?? [];
        $groups = $reminder->group_sources ?? [];

        if (!in_array($sender->id, $students)) {
            $students[] = $sender->id;
        }

        if ($conversation->type === 'group' && $conversation->name && !in_array($conversation->name, $groups)) {
            $groups[] = $conversation->name;
        }

        $reminder->unreplied_students = $students;
        $reminder->group_sources = $groups;

        // Build summary & description
        $count = count($students);
        $summary = "📩 Ada {$count} mahasiswa menunggu balasan di EduForum";

        $allStudentNames = User::whereIn('id', $students)->pluck('name')->toArray();
        $description = "Mahasiswa yang menunggu balasan:\n";
        foreach ($allStudentNames as $name) {
            $description .= "- {$name}\n";
        }

        if (!empty($groups)) {
            $description .= "\nSumber Grup:\n- " . implode("\n- ", $groups);
        }

        $description .= "\n\nBalas segera di: " . config('app.url') . "/forum";

        if (!$reminder->event_id) {
            // Buat event baru (multi-hari / all-day event)
            $eventId = $googleService->createMultiDayEvent(
                $recipient,
                $summary,
                $description,
                $startDate,
                $endDate
            );

            if ($eventId) {
                $reminder->event_id = $eventId;
                Log::info("[Eskalasi Tahap 2] Calendar event created: {$eventId} ({$startDate->format('Y-m-d')} s/d {$endDate->format('Y-m-d')})");
            }
        } else {
            // Update event yang sudah ada, sekaligus geser rentang tanggal agar tetap relevan
            $googleService->updateEvent(
                $recipient,
                $reminder->event_id,
                $summary,
                $description,
                $startDate,
                $endDate
            );
            Log::info("[Eskalasi Tahap 2] Calendar event updated: {$reminder->event_id} ({$startDate->format('Y-m-d')} s/d {$endDate->format('Y-m-d')})");
        }

        $reminder->save();
    }
}
