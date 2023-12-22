<?php

namespace App\Models\Authenication\Enum;

enum RoleEnum: string
{
    case CUSTOMER = 'CUSTOMER';
    case ADMIN = 'ADMIN';
    case SUPERADMIN = 'SUPERADMIN';
}
