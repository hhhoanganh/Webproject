<?php

namespace App\Http\Controllers;

use App\Models\Cart\Cart;
use App\Models\Order\Order;
use App\Models\Order\OrderItems;
use App\Models\Order\OrderStatus;
use App\Models\Product\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function addOrders(Request $request)
    {
        $user = Auth::user();
        $this->validate($request, [
            'products' => 'required|array',
            'products.*.product_id' => 'required|integer',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        if ($user === null) {
            return $this->sendError("No AUTHORIZED", AppConstant::UNAUTHORIZED_CODE);
        }
        $coupon =  $user->coupon;

        // Create a new order
        $order = Order::updateOrCreate(
            [
                'user_id' => $user->id,
                'status' => OrderStatus::PENDING,
            ]
        );
        if ($request["coupon_used"] === 1) {
            if(!$coupon) {
                return $this->sendError("You dont have any coupon");
            }
        }

        $order->coupon_used = $request["coupon_used"];

        foreach ($request->products as $product) {
            $orderItem = OrderItems::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'product_id' => $product['product_id'],
                ],
                [
                    'quantity' => $product['quantity'],
                    'price' =>0
                ]
            );

            $productModel = Product::find($product['product_id']);
            $orderItem->price = $productModel->price * $orderItem->quantity;

            $orderItem->save();
        }

        $order->total = $order->orderItems->sum('price');
        $order->save();
        $token = strtoupper(Str::random(6));
        if ($order->status === OrderStatus::PENDING) {
            MailController::sendSignUpEmail($user->name, $user->email, $token);
        }
        return $this->sendSuccess($order);
    }

    function getOrders()
    {
        $order = Order::where('user_id',Auth::user()->id)->get();
        return $this->sendSuccess($order);
    }

    function getOrder(Request $request)
    {
        $order = Order::where('id',$request['id'])->get();
        return $this->sendSuccess($order);
    }

    public function verifyOrder(Request $request)
    {
        $user = Auth::user();
        $order = Order::where('code',$request['token'])->get();
        $carts = Cart::where('user_id', $user->id)->get();
        if ($carts) {
            $cartIds = $carts->map(function ($cart) {
                return $cart->id;
            });
            // Delete the carts using the IDs
            Cart::whereIn('id', $cartIds)->delete();
        }
        if ($order) {
            $order->status = OrderStatus::WAIT_CONFIRMED;
            $order->save();
        }

        return $this->sendSuccess($order,null,"Order has confirmed");
    }

    function changeStatusOrder(Request $request)
    {
        $order = Order::where('code',$request['code']);
        $user = User::where($order->user_id)->get();
        if ($order) {
            if ($order->status === OrderStatus::CONFIRMED) {
                $order-> status = OrderStatus::SHIPPING;
                MailController::sendSignUpEmail($user->name, $user->email, $request->input('code'));
                $order->save();
                return $this->sendSuccess($order,null,"Order has changed status: SHIPPING");
            }
            if ($order->status === OrderStatus::SHIPPING) {
                $order-> status = OrderStatus::COMPLETED;
                $order-> refusal_reason = null;
                $order->save();
                $user = User::find($order->user_id);
                $point_total = $order->orderItems->sum('quantity') + $user->point;
                $user->point = $point_total;
                $user->save();
                return $this->sendSuccess($order,null,"Order has changed status: COMPLETED");
            }
        }

    }

}
