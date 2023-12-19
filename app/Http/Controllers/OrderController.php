<?php

namespace App\Http\Controllers;

use App\Models\Authenication\Enum\Status;
use App\Models\Order\Order;
use App\Models\Order\OrderItems;
use App\Models\Order\OrderStatus;
use App\Models\Product\Product;
use App\Models\Response\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'products.*.quantity' => 'required|integer|min:1'
        ]);
        if($user === null) {
            return $this->sendError("No AUTHORIZED", AppConstant::UNAUTHORIZED_CODE);
        }
        // Create a new order
        $order = Order::updateOrCreate(
            [
                'user_id' => $user->id,
                'status' => OrderStatus::PENDING,
            ]
        );

        foreach ($request->products as $product) {
            // Create a new order item
            $orderItem = new OrderItems();
            $orderItem->product_id = $product['product_id'];
            $orderItem->quantity = $product['quantity'];

            // Fetch the product's price and calculate the total price for the order item
            $productModel = Product::find($product['product_id']);
            $orderItem->price = $productModel->price * $orderItem->quantity;

            // Save the order item
            $order->orderItems()->save($orderItem);
        }

        // Calculate and save the total price for the order
        $order->total = $order->orderItems->sum('price');
        $order->save();
//        if ($order == OrderStatus::PENDING) {
//            MailController::sendSignUpEmail($user->name, $user->email,$order);
//        }
        return $this->sendSuccess($order);
    }

    public function salesOrders(){
        $totals = Order::where('status', 'completed')
        ->with('orderItems') // Load mối quan hệ OrderItem
        ->get()
        ->flatMap(function ($order) {
            return $order->orderItems->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            })->map(function ($items) {
                return [
                'order_date' => $items->first()->created_at->format('Y-m-d'),
                'total_price' => $items->sum('price'),
                ];
            });
        });
        return $this->sendSuccess($totals);
    }
}
