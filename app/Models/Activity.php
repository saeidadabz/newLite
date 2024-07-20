<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'event_id',
        'state',
        'event_type',
        'user_id',
        'workspace_id',
        'user_id',
        'room_id',
        'data',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);

    }

    public function room()
    {
        return $this->belongsTo(Room::class);

    }
}
