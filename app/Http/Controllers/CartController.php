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
        $this->validate($request, [
            'product_id' => 'required',
            'quantity' => 'required|numeric',
        ]);
        $user = Auth::user();
        $product = Product::find($request['product_id']);
        $cartItem = Cart::where("product_id", $request['product_id'])->first();

        if ($cartItem !== null) {
            $cartItem->quantity += $request['quantity'];
            $cartItem->price += $product->price * $request['quantity'];
            $cartItem->save();

            return $this->sendSuccess($cartItem, null, 'Product added to cart successfully');
        }

        $cartItem = Cart::create([
            'user_id' => $user->id,
            'product_id' => $request->input('product_id'),
            'quantity' => $request->input('quantity'),
            'price' => $product->price * $request->input('quantity'),
        ]);

        return $this->sendSuccess($cartItem, null, 'Product added to cart successfully');
    }


    public function getCart()
    {

        $user = Auth::user();
        $cart = Cart::where('user_id', '=', $user->id)->get();
        return $this->sendSuccess($cart,null,);
    }
}
