<?php

namespace App\Utilities;

use App\Models\Room;
use App\Models\User;

class Participant
{


    public function __construct($args = [])
    {

        foreach ($args as $key => $value) {
            $this->{$key} = $value;
        }

    }

    public function __get($key)
    {
        if (isset($this->args->{$key})) {
            return $this->args->{$key};
        }
        return NULL;

    }


}
