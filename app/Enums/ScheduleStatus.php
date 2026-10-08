<?php

namespace App\Enums;

enum ScheduleStatus: string
{
    case SCHEDULED = 'scheduled';
    case CANCELLED = 'cancelled';
}
