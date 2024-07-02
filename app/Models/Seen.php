<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seen extends Model
{


    protected $fillable = [
        'room_id',
        'user_id',
        'message_id'
    ];


    public function room()
    {
        return $this->belongsTo(Room::class);

    }

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
