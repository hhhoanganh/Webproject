<?php

namespace App\Models\Authenication\Enum;

enum Status: int
{
    case INACTIVE = 0;
    case ACTIVE = 1;
    case LOCKED = 2;
}
