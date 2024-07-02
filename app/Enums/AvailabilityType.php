<?php

namespace App\Enums;

enum AvailabilityType: int
{
    case VIDEO = 0;

    case VOICE = 1;

    case TEXT = 2;
}
