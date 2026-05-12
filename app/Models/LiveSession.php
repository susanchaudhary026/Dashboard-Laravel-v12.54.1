<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveSession extends Model
{
    protected $fillable = [
        'title',
        'room_name',
        'is_live',
        'meeting_link',
        'user_id',
        'started_at',
        'ended_at'
    ];

    protected $casts = [
        'is_live' => 'boolean',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);

    }

    public function isActive()
    {
        return $this->is_live === true;

    }

    public function getDurationInMinutes()
    {
        if (!$this->started_at){
            return 0;
        }
        $endTime = $this->ended_at ?? now();
        return $this->started_at->diffInMinutes($endTime);
    }

}
