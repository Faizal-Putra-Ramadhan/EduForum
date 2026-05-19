<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LecturerReminder extends Model
{
    protected $fillable = [
        'lecturer_id',
        'event_id',
        'unreplied_students',
        'group_sources',
    ];

    protected $casts = [
        'unreplied_students' => 'array',
        'group_sources' => 'array',
    ];

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }
}
