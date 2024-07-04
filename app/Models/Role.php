<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public const PERMISSIONS = [
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
        'update-messages-in-room', //delete message,pin message,edit message
        'add-job-to-ws',
        'get-job',
        'invite-member-to-job',
        'remove-member-from-job',
        'update-job',
        'delete-job',

    ];

    public const ROLES = [
        'member' => [
            'get-ws',
            'get-room',
            'add-job-to-ws',
            'get-job',
            'invite-member-to-job',
        ],
        'admin' => [
            'get-ws',
            'add-member-to-ws',
            'remove-member-from-ws',
            'add-rooms-to-ws',
            'change-rooms-position',
            'get-room',
            'update-room',
            'delete-room',
            'update-messages-in-room',
        ],

        'owner' => ['*'],
    ];

    protected $fillable = [
        'title',
        'description',
    ];
}
