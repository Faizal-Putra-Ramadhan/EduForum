<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $connection = 'sqlite_messages';

    protected $fillable = [
        'name', 'type', 'avatar', 'creator_id', 'last_message_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function conversationUsers()
    {
        return $this->hasMany(ConversationUser::class, 'conversation_id');
    }

    // Get users across connections manually
    public function getUsersAttribute()
    {
        return User::whereIn('id', $this->conversationUsers->pluck('user_id'))->get();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getIsGroupAttribute()
    {
        return $this->type === 'group';
    }

    public function getDisplayNameAttribute()
    {
        if ($this->is_group) {
            return $this->name ?? 'Grup Tanpa Nama';
        }

        $otherMapping = $this->conversationUsers->where('user_id', '!=', auth()->id())->first();
        return $otherMapping ? ($otherMapping->user->name ?? 'Chat') : 'Chat';
    }
}
