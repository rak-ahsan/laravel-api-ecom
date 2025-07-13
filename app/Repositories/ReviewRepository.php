<?php

namespace App\Repositories;

use Exception;
use App\Models\Review;
use App\Classes\Helper;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class ReviewRepository
{
    public function __construct(protected Review $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $productId    = $request->input('product_id', null);
        $userId       = $request->input('user_id', null);

        try {
            $reviews = $this->model->with(['product:id,name', 'user:id,name'])
            ->when($productId, fn ($query) => $query->where("product_id", $productId))
            ->when($userId, fn ($query) => $query->where("user_id", $userId))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $reviews;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $review = new $this->model();

            $review->user_id    = $request->user_id;
            $review->product_id = $request->product_id;
            $review->rate       = $request->rate;
            $review->comment    = $request->comment;
            $review->status     = $request->status;
            $review->save();

            DB::commit();

            return $review;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $review = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$review) {
                throw new CustomException("Review not found");
            }

            return $review;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $review = $this->model->find($id);

            if (!$review) {
                throw new CustomException("Review not found");
            }

            $review->user_id    = $request->user_id;
            $review->product_id = $request->product_id;
            $review->rate       = $request->rate;
            $review->comment    = $request->comment;
            $review->status     = $request->status;
            $review->save();

            DB::commit();

            return $review;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $review = $this->model->find($id);
            if (!$review) {
                throw new CustomException('Review not found');
            }

            return $review->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $productId    = $request->input('product_id', null);
        $userId       = $request->input('user_id', null);

        try {
            $reviews = $this->model->with(['product:id,name', 'user:id,name'])
            ->onlyTrashed()
            ->when($productId, fn($query) => $query->where("product_id", $productId))
            ->when($userId, fn($query) => $query->where("user_id", $userId))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $reviews;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $review = $this->model->onlyTrashed()->find($id);
            if (!$review) {
                throw new CustomException('Review not found');
            }

            $review->restore();

            return $review;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $review = $this->model->withTrashed()->find($id);
            if (!$review) {
                throw new CustomException('Review not found');
            }

            return $review->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
