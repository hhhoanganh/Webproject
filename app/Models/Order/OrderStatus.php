<?php

namespace App\Models\Order;

enum OrderStatus:String
{
    case PENDING = 'PENDING';
    case WAIT_CONFIRMED = 'WAIT_CONFIRMED';
    case CONFIRMED = 'CONFIRMED';
    case SHIPPING = 'SHIPPING';
    case CANCEL = 'CANCEL';
    case COMPLETED = 'COMPLETED';
}
