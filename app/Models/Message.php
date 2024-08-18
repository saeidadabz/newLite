<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{


    use SoftDeletes;

    protected $fillable = [
        'text',
        'room_id',
        'user_id',
        'is_edited',
        'reply_to',
        'is_pinned',
        'created_at',
        'deleted_at',
        'updated_at',
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

    public function mentions()
    {
        return $this->hasMany(Mention::class);
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'reply_to');
    }

    public function seens()
    {
        return $this->hasMany(Seen::class);
    }

    public function saw($user)
    {
        return $this->seens()->whereUserId($user->id)->first() !== null;

    }
}
