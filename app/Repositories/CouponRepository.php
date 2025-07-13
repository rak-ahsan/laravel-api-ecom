<?php

namespace App\Repositories;

use Exception;
use App\Models\Coupon;
use App\Classes\Helper;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class CouponRepository
{
    public function __construct(protected Coupon $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $brands = $this->model->with(["createdBy:id,username"])
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("name", "like", "%$searchKey%")
                    ->orWhere("status", $searchKey);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $brands;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $coupon = new $this->model();

            $coupon->name            = $request->name;
            $coupon->code            = $request->code;
            $coupon->status          = $request->status;
            $coupon->discount_type   = $request->discount_type;
            $coupon->discount_amount = $request->discount_amount;
            $coupon->min_cart_amount = $request->min_cart_amount;
            $coupon->started_at      = $request->started_at;
            $coupon->ended_at        = $request->ended_at;
            $coupon->description     = $request->description;
            $coupon->save();

            DB::commit();

            return $coupon;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $coupon = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$coupon) {
                throw new CustomException("Coupon not found");
            }

            return $coupon;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $coupon = $this->model->find($id);
            if (!$coupon) {
                throw new CustomException("Coupon Not found");
            }

            $coupon->name            = $request->name;
            $coupon->code            = $request->code;
            $coupon->status          = $request->status;
            $coupon->min_cart_amount = $request->min_cart_amount;
            $coupon->discount_type   = $request->discount_type;
            $coupon->discount_amount = $request->discount_amount;
            $coupon->started_at      = $request->started_at;
            $coupon->ended_at        = $request->ended_at;
            $coupon->description     = $request->description;
            $coupon->save();

            DB::commit();

            return $coupon;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $coupon = $this->model->find($id);
            if (!$coupon) {
                throw new CustomException('Coupon not found');
            }

            return $coupon->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey = $request->input('search_key', null);

        try {
            $brands = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("name", "like", "%$searchKey%")
                    ->orWhere("status", $searchKey);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $brands;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $coupon = $this->model->onlyTrashed()->find($id);
            if (!$coupon) {
                throw new CustomException('Coupon not found');
            }

            $coupon->restore();

            return $coupon;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $coupon = $this->model->withTrashed()->find($id);
            if (!$coupon) {
                throw new CustomException('Coupon not found');
            }

            return $coupon->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
