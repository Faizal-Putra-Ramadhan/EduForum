<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Conversation;
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
     */
    public function handle(WhatsAppService $waService): void
    {
        // Final check: has the message been replied to by now?
        $hasReply = Message::where('conversation_id', $this->message->conversation_id)
            ->where('sender_id', '!=', $this->message->sender_id)
            ->where('created_at', '>', $this->message->created_at)
            ->exists();

        if ($hasReply) {
            Log::info("Message ID {$this->message->id} has been replied to by the scheduled time. Skipping WA reminder.");
            return;
        }

        // Get recipient
        $conversation = Conversation::with('conversationUsers.user')->find($this->message->conversation_id);
        if (!$conversation) return;

        $recipientMapping = $conversation->conversationUsers->where('user_id', '!=', $this->message->sender_id)->first();
        $recipient = $recipientMapping ? $recipientMapping->user : null;

        if (!$recipient || empty($recipient->phone)) {
            Log::warning("Cannot send WA reminder for message {$this->message->id}: No recipient or phone number.");
            return;
        }

        Log::info("Message ID {$this->message->id} still not replied to on scheduled date. Sending WA reminder to {$recipient->name}");

        $waMsg = "Halo {$recipient->name}, ini pengingat dari EduForum. Anda memiliki pesan yang belum dibalas dari " . $this->message->sender->name . " sejak kemarin. Silakan balas segera!";
        
        $waService->sendMessage($recipient->phone, $waMsg);
    }
}
