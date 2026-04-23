<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'message_id', 'recipient_id', 'type', 'attempt'
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
