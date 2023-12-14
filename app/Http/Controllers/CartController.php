<?php
namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\CartResponse;
use Exception;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cart;
    public function __construct()
    {
        $this->cart = new CartItem();
    }

    public function getAllCart($id)
    {
        $cartItems = $this->cart->where('USER_ID', $id)->get();
        if ($cartItems->isNotEmpty()) {
            $firstData = $cartItems->shift();
            $cartUserId = $firstData->user_id;
            $cartQuantity = $firstData->quantity;
            $cartPrice = $firstData->price;
            $cart = new CartResponse($cartUserId, $cartPrice, $cartQuantity);
            $product_idList = [];
            foreach ($cartItems as $item) {
                array_push($product_idList, $item->product_id);
            }
            $cart->addList($product_idList);
            return response()->json(['message' => 'successfull', $cart]);
        }
        return response()->json(['message' => 'error']);
    }

    public function addToCart(Request $request,$id, $product)
    {
        try {
            $newCart = new CartItem([
                'USER_ID' => $request->input($id),
                'PRODUCT_ID' => $request->input($product->product_id),
                'QUANTITY' => $request->input($product->quantity),
                'PRICE' => $request->input($product->price)
            ]);
            return response()->json(['message' => 'successfull']);
        } catch(Exception $e){
            return response()->json(['message' => $e]);
        }
    }

    public function deleteCart(Request $request,$id, $user_id){
        try {
            $deleted = $this->cart->where('USER_ID', $user_id)
                               ->where('PRODUCT_ID', $id)
                               ->delete();
            if ($deleted){
                return response()->json(['message' => 'successfull']);
            }
            return response()->json(['message' => 'successfull']);
        } catch(Exception $e){
            return $e;
        }
    }
}
