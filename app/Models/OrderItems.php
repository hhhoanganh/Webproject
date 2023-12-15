<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OderItems extends Model
{
    protected $table = 'orderitems';

    protected $fillable = [
        'product_id',
        'price',
        'quantity',
        'column_5',
        'order_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
