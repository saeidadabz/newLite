<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    const permissions = [
        'get-ws',
        'add-member-to-ws',
        'remove-member-from-ws',
        'update-ws',
        'delete-ws',
        'add-rooms-to-ws',
        'change-rooms-position',
        'get-room',
        'kick-user-from-room',
        'update-room',
        'delete-room',
        'update-user-in-room', //mute,unmute,stop screen,change position of user.
        'update-messages-in-room' //delete message,ping message,edit message

    ];
}
