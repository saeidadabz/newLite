<?php

namespace App\Utilities;

use App\Enums\Permission;

class Constants {
    public const ROLE_PERMISSIONS = [
        'member'      => [
            Permission::WS_GET,
            Permission::ROOM_GET,
            Permission::WS_ADD_JOB,
            Permission::JOB_GET,
            Permission::JOB_INVITE_MEMBER,
        ],
        'admin'       => [
            Permission::WS_GET,
            Permission::WS_ADD_MEMBER,
            Permission::WS_REMOVE_MEMBER,
            Permission::WS_ADD_ROOMS,
            Permission::ROOMS_CHANGE_POSITION,
            Permission::ROOM_GET,
            Permission::ROOM_UPDATE,
            Permission::ROOM_DELETE,
            Permission::ROOM_UPDATE_MESSAGES,
        ],
        'super-admin' => [
            Permission::ALL,
        ],
    ];


    const JOINED = 'participant_joined';
    const LEFT = 'participant_left';

    const BASE_DATE_FORMAT = 'H:i:s';
    const SCHEDULE_DATE_FORMAT = 'H:i:s';
    const ONLINE = 'online';
    const OFFLINE = 'offline';

    const API_SUCCESS_MSG = 'success';
    const API_FAILED_MSG = 'failed';

    const joinedRoom = 'joinedRoom';
    const userUpdated = 'userUpdated';
    const workspaceUpdated = 'workspaceUpdated';
    const roomUpdated = 'roomUpdated';
    const roomMessages = 'roomMessages';
    const directMessages = 'directMessages';
    const messageUpdated = 'messageUpdated';
    const messageDeleted = 'messageDeleted';
    const messagePinned = 'messagePinned';
    const messageSeen = 'messageSeen';
    const workspaceRoomUpdated = 'workspaceRoomUpdated';

}
