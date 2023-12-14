<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CartResponse extends Model
{
    protected $user_id;
    protected $quantity;
    protected $product_id = [];
    protected $price;

    public function __construct()
    {
        $this->product_id = array();
    }

    public function Cart($user_id, $price, $quantity)
    {
        $this->user_id = $user_id;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function add_product($product_id)
    {
        if (!in_array($product_id, $this->product_id)) {
            array_push($this->product_id, $product_id);
        }
    }

    public function remove_product($product_id)
    {
        if (in_array($product_id, $this->product_id)) {
            $key = array_search($product_id, $this->product_id);
            unset($this->product_id[$key]);
        }
    }

    public function toString()
    {
        return "Cart Information:\nName: $this->name\nPrice: $this->price\nID: $this->id\nProduct IDs: " . implode(", ", $this->product_id);
    }

    function addList($list) {
        foreach ($list as $item) {
            array_push($product_id, $item);
        }
    }

}
