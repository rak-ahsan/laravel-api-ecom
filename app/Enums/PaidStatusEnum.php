<?php

namespace App\Enums;

enum PaidStatusEnum : string
{
    case PAID   = 'paid';
    case UNPAID = 'unpaid';
}
