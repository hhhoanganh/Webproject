<?php

namespace App\Models;

use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function order()
    {
        return $this->hasMany(Order::class);
    }
}
