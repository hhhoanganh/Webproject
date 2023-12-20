<?php

namespace App\Models\Product;

use App\Models\Cart\Cart;
use App\Models\Order\OrderItems;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';

    protected $fillable = [
        'name',
        'code',
        'description',
        'thumbnail',
        'price',
    ];
    protected $guarded = ['id'];

    protected $visible = ['code','name','description','thumbnail','price','reviews'];

    public function orderItems()
    {
        return $this->hasMany(OrderItems::class);
    }

    public function images()
    {
        return $this->hasMany(Images::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}
