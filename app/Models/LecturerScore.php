<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LecturerScore extends Model
{
    protected $fillable = [
        'user_id', 'total_xp', 'total_replies', 'avg_response_hours', 'badge'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function adjustXP($userId, $amount, $reason)
    {
        $score = self::firstOrCreate(
            ['user_id' => $userId],
            ['total_xp' => 0, 'total_replies' => 0]
        );

        $score->increment('total_xp', $amount);

        XpLog::create([
            'lecturer_id' => $userId,
            'xp_earned' => $amount,
            'reason' => $reason,
        ]);

        return $score;
    }
}
