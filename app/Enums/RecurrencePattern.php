<?php

namespace App\Enums;

enum RecurrencePattern: int
{
    case Daily = 0;

    case Weekly = 1;

    case Monthly = 2;

    case CUSTOM = 3;
}
