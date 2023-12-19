<?php

namespace App\Http\Controllers;

use App\Models\Cart\Cart;
use App\Models\Product\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function addCart(Request $request)
    {
        $this->validate($request,[
            'product_id' => 'required',
            'quantity' => 'required|numeric'
        ]);
        $user = Auth::user();
        $product = Product::find($request->input('product_id'));

        $cartItem = Cart::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $product->id,
            ],
            [
                'quantity' => $request->input('quantity'),
                'price' => $product->price * $request->input('quantity')
            ]
        );
        return $this->sendSuccess($cartItem,null,'Product added to cart successfully');
    }

    public function getCart()
    {

        $user = Auth::user();
        $cart = Cart::where('user_id', '=', $user->id)->get();
        return $this->sendSuccess($cart,null,);
    }
}
