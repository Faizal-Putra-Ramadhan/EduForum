<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationUser extends Model
{
    protected $connection = 'sqlite_messages';
    protected $table = 'conversation_users';

    protected $fillable = [
        'conversation_id', 'user_id', 'role'
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
