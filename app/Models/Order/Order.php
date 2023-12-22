<?php

namespace App\Models\Order;

use App\Models\Note;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = ['id'];


    protected $table = "order";
    protected $fillable = [
        'user_id',
        'total',
        'address',
        'status',
        'code'
    ];

//    protected $visible = [
//        'id',
//        'user_id',
//        'total',
//        'address',
//        'status',
//        'order_items'
//
//    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItems::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

}
