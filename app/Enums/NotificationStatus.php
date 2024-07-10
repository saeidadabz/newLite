<?php

namespace App\Enums;

enum NotificationStatus: int
{
    case Success = 0;
    case Pending = 1;
    case Scheduled = 2;
    case Failed = 3;
}
