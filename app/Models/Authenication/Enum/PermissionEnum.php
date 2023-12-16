<?php

namespace App\Models\Authenication\Enum;

enum PermissionEnum:string
{
    case READ = 'READ';
    case CREATE = 'CREATE';
    case UPDATE = 'UPDATE';
    case DELETE = 'DELETE';
}
