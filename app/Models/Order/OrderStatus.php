<?php

namespace App\Models\Order;

enum OrderStatus:String
{
    case PENDING = 'PENDING';
    case CONFIRMED = 'CONFIRMED';
    case SHIPPING = 'SHIPPING';
    case COMPLETED = 'COMPLETED';
}
