<?php

namespace App\Models\Order;

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
        'address'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItems::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }


    public function status()
    {
        return $this->belongsTo(Status::class);
    }

}
