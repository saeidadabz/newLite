<?php

namespace App\Utilities;

use App\Models\Room;
use App\Models\User;

class EventType
{
    public function __construct(...$args)
    {

        $this->args = json_decode(json_encode($args))[0];
    }

    public function __get($key)
    {
        if (isset($this->args->{$key})) {
            return $this->args->{$key};
        }
        return NULL;

    }

    public function hasParticipant()
    {
        return $this->participant !== null;

    }

    public function participant()
    {
        return new Participant();

    }

    public function room()
    {
        return Room::find($this->room->name);
    }

    public function user()
    {
        if (isset($this->args->participant)) {
            return User::where('username', $this->args->participant?->identity)->first();

        }
        return null;
    }


}
