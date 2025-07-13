<?php

namespace App\Enums;

enum OrderGuardTypeEnum: string
{
    case MINUTES = "minutes";
    case HOURS   = "hours";
    case DAYS    = "days";
}
