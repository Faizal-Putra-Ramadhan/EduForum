<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\LecturerReminder;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppReminderJob implements ShouldQueue
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
     * TAHAP 3: Kirim WhatsApp reminder ke dosen jika masih belum dibalas
    * setelah melewati periode Calendar reminder (4 hari).
     */
    public function handle(WhatsAppService $waService): void
    {
        // Final check: apakah dosen sudah membalas?
        $hasReply = Message::where('conversation_id', $this->message->conversation_id)
            ->where('sender_id', '!=', $this->message->sender_id)
            ->where('created_at', '>', $this->message->created_at)
            ->exists();

        if ($hasReply) {
            Log::info("[Eskalasi Tahap 3] Message ID {$this->message->id} sudah dibalas. Skip WA reminder.");
            return;
        }

        // Cari dosen (recipient)
        $conversation = Conversation::with('conversationUsers.user')->find($this->message->conversation_id);
        if (!$conversation) return;

        $recipientMapping = $conversation->conversationUsers->where('user_id', '!=', $this->message->sender_id)->first();
        $recipient = $recipientMapping ? $recipientMapping->user : null;

        if (!$recipient || empty($recipient->phone)) {
            Log::warning("[Eskalasi Tahap 3] Tidak bisa kirim WA untuk message {$this->message->id}: Tidak ada recipient atau nomor HP.");
            return;
        }

        $sender = $this->message->sender;
        $daysSince = now()->diffInDays($this->message->created_at);

        Log::info("[Eskalasi Tahap 3] Message ID {$this->message->id} belum dibalas setelah {$daysSince} hari. Mengirim WA ke {$recipient->name}");

        $waMsg = "Halo {$recipient->name}, ini pengingat dari EduForum.\n\n"
            . "Anda memiliki pesan yang belum dibalas dari *{$sender->name}* sejak {$daysSince} hari yang lalu.\n\n"
            . "Pesan: \"{$this->message->content}\"\n\n"
            . "Silakan balas segera di: " . config('app.url') . "/forum/{$conversation->id}";

        $waService->sendMessage($recipient->phone, $waMsg);

        // Tetap simpan data reminder; tidak ada penghapusan event Google Calendar otomatis
        $reminder = LecturerReminder::where('lecturer_id', $recipient->id)->first();
        if ($reminder && $reminder->event_id) {
            Log::info("[Eskalasi Tahap 3] Event Calendar {$reminder->event_id} tetap dipertahankan setelah WA terkirim.");
        }
    }
}
