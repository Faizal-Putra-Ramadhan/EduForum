<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $connection = 'mysql';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'nim',
        'nidn',
        'role',
        'avatar',
        'prodi',
        'password',
        'google_id',
        'google_token',
        'google_refresh_token',
        'google_token_expires_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'google_token_expires_at' => 'datetime',
        ];
    }

    public function score()
    {
        return $this->hasOne(LecturerScore::class, 'user_id');
    }

    public function xpLogs()
    {
        return $this->hasMany(XpLog::class, 'lecturer_id');
    }

    public function userConversations()
    {
        return $this->hasMany(ConversationUser::class, 'user_id');
    }

    public function lecturerScore()
    {
        return $this->hasOne(LecturerScore::class, 'user_id');
    }

    public function getResponsivenessTagAttribute()
    {
        $score = $this->lecturerScore?->total_xp ?? 0;

        if ($score >= 100) return 'Sangat Fast Respon';
        if ($score >= 50) return 'Cukup Fast Respon';
        if ($score >= 30) return 'Lumayan Fast Respon';
        if ($score >= 15) return 'Kurang Fast Respon';
        if ($score >= 5) return 'Sangat Tidak Fast Respon';
        
        return 'Belum ada Tag';
    }
}
