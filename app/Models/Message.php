<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    protected $fillable = [
        'text', 'room_id', 'user_id', 'edited', 'reply_to'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replyTo()
    {
        return $this->belongsTo(Message::class, 'reply_to');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'reply_to');
    }
}
