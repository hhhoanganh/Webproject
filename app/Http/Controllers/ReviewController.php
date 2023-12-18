<?php

namespace App\Http\Controllers;

use App\Models\Authenication\Enum\RoleEnum;
use App\Models\Authenication\Enum\Status;
use app\Models\Authenication\Role;
use App\Models\Order\Order;
use App\Models\Order\OrderStatus;
use App\Models\Product\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{

    public function productReview(Request $request)
    {
        $user = Auth::user();
        $product = Product::find($request['product_id']);
        $reviews = $product->review;

        if (count($reviews) == 0) {
            $rate = 0;
        } else {
            $rate = $reviews->sum("rating") / count($reviews);
        }

        $isPurchased = $this->isPurchased($user, $product);
        $isReviewed = $this->isReviewed($user, $product);

        $starCounter = [];
        $sum = 0;
        for ($i = 1; $i <= 5; $i++) {
            $total = count(Review::where(["rating" => $i, "product_id" => $product->id])->get());
            array_push($starCounter,  $total);
            $sum += $total;
        }

        return $this->sendSuccess([$reviews,
            'isPurchased' => $isPurchased,
            'isReviewed' => $isReviewed,
            'sum'=>$sum
        ],null,"Product Review");
    }

    public function addReview(Request $request)
    {
        $validatedData = $request->validate([
            "rating" => "required|min:1|max:5",
            "review" => "required"
        ]);

        $validatedData["user_id"] = auth()->user()->id;
        $validatedData["product_id"] = $request->product_id;
        $validatedData["is_edit"] = 0;

        $review = Review::create($validatedData);

        $message = "Your review has been created!";
        return $this->sendSuccess($review,null,$message);
    }

    public function editReview(Request $request)
    {
        $review = Review::where('product_id', $request['product_id'],'user_id',auth()->user()->id)->get();
        $review->fill([
            'rating' => $request->rating,
            'review' => $request->review_edit,
            'is_edit' => 1,
        ]);

        if ($review->isDirty()) {
            $review->save();

            $message = "Your review has been updated!";
            return $this->sendSuccess($review,null,$message);
        } else {
            $message = "Action failed, no changes detected!";
            return $this->sendError($message);
        }
    }


    public function deleteReview(Request $request)
    {
        $review = Review::where('id',$request['id'])->get();
        if($review->user_id === Auth::user()->id || Auth::user()->roles() === RoleEnum::SUPERADMIN){
            $review->delete();
            $message = "Your review has been deleted!";
            return $this->sendSuccess(null,null,$message);
        }



    }
    private function isPurchased($user, $product)
    {
        $orders = Order::where(["user_id" => $user->id, "product_id" => $product->id, 'status' => OrderStatus::COMPLETED])->get();

        if (count($orders) > 0) {
            return 1;
        }

        return 0;
    }


    private function isReviewed($user, $product)
    {
        $review = Review::where(["user_id" => $user->id, "product_id" => $product->id])->get();

        if (count($review) > 0) {
            return 1;
        }

        return 0;
    }
}
