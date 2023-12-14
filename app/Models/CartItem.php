<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'cart';

    protected $fillable =[
       'user_id',
       'product_id',
       'quantity',
       'price',
    ];
}
