<?php

namespace App\Http\Controllers;

use App\Models\Authenication\Enum\Status;
use App\Models\Cart\Cart;
use App\Models\Order\Order;
use App\Models\Order\OrderItems;
use App\Models\Order\OrderStatus;
use App\Models\Product\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PHPUnit\TextUI\Configuration\Constant;

class OrderController extends Controller
{
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
        $order = Order::create(
            [
                'user_id' => $user->id,
                'status' => OrderStatus::PENDING,
            ]
        );
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
        $token = strtoupper(Str::random(6));
        $order->code = $token;
        if ($request["coupon_used"] === 1) {
            if(!$coupon) {
                return $this->sendError("You dont have any coupon");
            }
            $order->total = ($order->orderItems->sum('price') * 50)/100;
            $user->coupon = $coupon - 1;
            MailController::sendOrderEmail($user->name, $user->email, $token,$order);
            $user->save();
            $order->save();
        }
        $total_before_sale = $order->orderItems->sum('price');
        $order->total = ($order->orderItems->sum('price') * 50)/100;
        MailController::sendOrderEmail($user->name, $user->email, $token,$order);
        $order->save();
        return $this->sendSuccess([$order,
            'total_before' => $total_before_sale
            ]);
    }

    function getOrders()
    {
        $order = Order::where('user_id',Auth::user()->id)->get();
        return $this->sendSuccess($order);
    }

    function getOrder(Request $request)
    {
        $order = Order::where('code',$request['code'])->get();
        return $this->sendSuccess($order);
    }

    public function verifyOrder(Request $request)
    {
        $order = Order::where('code', $request['token'])->first();
        $id = $order->user_id;
        $carts = Cart::where('user_id', $id)->get();
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
        $this->validate($request,[
            'code'=>'required'
        ]);
        $order = Order::where('code', $request['code'])->first();
        $id = $order->user_id;
        if ($order->status == OrderStatus::WAIT_CONFIRMED->name) {
            $order-> status = OrderStatus::CONFIRMED;
            $order->save();
            return $this->sendSuccess($order,null,"Order has changed status: CONFIRM");
        }
        if ($order->status === OrderStatus::CONFIRMED->name) {
            $order-> status = OrderStatus::SHIPPING;
            $order->save();
            return $this->sendSuccess($order,null,"Order has changed status: SHIPPING");
        }
        if ($order->status == OrderStatus::SHIPPING->name) {
            $order-> status = OrderStatus::COMPLETED;
            $order-> refusal_reason = null;
            $user = User::where('id',$id)->first();
            $point_total = $order->orderItems->sum('quantity') + $user->point;
            $user->point = $point_total;
            $user->save();
            $order->save();
            return $this->sendSuccess($order,null,"Order has changed status: COMPLETED");
        }
        return $this->sendError('Order not found',AppConstant::NOT_FOUND_CODE);
    }

    public function cancelOrder(Request $request)
    {
        $this->validate($request, [
            'reason' => 'required',
        ]);
        $user = Auth::user();
        $order = Order::where('code',$request['code'])->first();
        if ($order->status === OrderStatus::WAIT_CONFIRMED->name ||
            $order->status === OrderStatus::CONFIRMED->name
            && auth()->user()->id === $order->user_id) {
            $order->status = OrderStatus::CANCEL;
            $order->refusal_reason = $request->input(['reason']);
            $user->coupon = $user->coupon + 1;
            $user-> save();
            $order->save();
            return $this->sendSuccess($order,null,'Order has cancelled');
        }
        return  $this->sendError("You can't cancel order",AppConstant::BAD_REQUEST_CODE);
    }
}
