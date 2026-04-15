<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XpLog extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'lecturer_id', 'xp_earned', 'reason', 'response_hours'
    ];

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }
}
