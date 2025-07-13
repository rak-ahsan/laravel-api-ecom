<?php

namespace App\Http\Controllers\Front;

use Exception;
use App\Models\Order;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ReviewController extends BaseController
{
    public function store(Request $request)
    {
        $rate      = $request->input('rate', 1);
        $productId = $request->input('product_id', null);
        $comment   = $request->input('comment', null);

        try {
            DB::beginTransaction();

            $product = Product::where('id', $productId)
                ->where('status', 'active')
                ->first();

            if (!$product) {
                return $this->sendError('Product not found');
            }

            $order = Order::join('order_item', 'orders.id', '=', 'order_item.order_id')
                ->where('orders.user_id', Auth::id())
                ->where('order_item.item_id', $productId)
                ->select('orders.*')
                ->first();

            if (!$order) {
                return $this->sendError('You can not rate this product without purchase');
            }

            $rating = Review::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->first();

            if ($rating) {
                $rating->rate = $rate;
                $rating->comment = $comment;
                $rating->save();
            } else {
                $rating = new Review;

                $rating->user_id    = Auth::id();
                $rating->rate       = $rate;
                $rating->product_id = $productId;
                $rating->comment    = $comment;
                $rating->save();
            }

            DB::commit();

            return $this->sendResponse(null, "Product review successfully");
        } catch (Exception $exception) {
            DB::commit();
            Log::error($exception->getMessage());

            return $this->sendError('Something went wrong');
        }
    }

}
