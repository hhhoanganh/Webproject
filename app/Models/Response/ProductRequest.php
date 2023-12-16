<?php

namespace App\Models\Response;

use App\Models\Product\Images;
use Illuminate\Database\Eloquent\Model;

class ProductRequest extends Model
{
    protected $fillable=[
        'product_id',
        'quantity'
    ];
}
