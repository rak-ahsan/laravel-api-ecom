<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\CouponRepository;
use App\Http\Requests\Admin\CouponRequest;
use App\Http\Resources\Admin\CouponResource;
use App\Http\Resources\Admin\CouponCollection;

class CouponController extends BaseController
{
    public function __construct(protected CouponRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('coupons-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $coupons = $this->repository->index($request);

            $coupons = new CouponCollection($coupons);

            return $this->sendResponse($coupons, 'Coupons list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(CouponRequest $request)
    {
        if (!$request->user()->hasPermission('coupons-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $coupon = $this->repository->store($request);

            $coupon = new CouponResource($coupon);

            return $this->sendResponse($coupon, 'Coupon created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('coupons-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $coupon = $this->repository->show($id);

            $coupon = new CouponResource($coupon);

            return $this->sendResponse($coupon, "Coupon single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(CouponRequest $request, $id)
    {
        if (!$request->user()->hasPermission('coupons-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $coupon = $this->repository->update($request, $id);

            $coupon = new CouponResource($coupon);

            return $this->sendResponse($coupon, 'Coupon updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('coupons-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $coupon = $this->repository->delete($id);

            return $this->sendResponse($coupon, 'Coupon deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('coupons-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $coupons = $this->repository->trashList($request);

            $coupons = new CouponCollection($coupons);

            return $this->sendResponse($coupons, 'Coupons trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('coupons-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $coupon = $this->repository->restore($id);

            $coupon = new CouponResource($coupon);

            return $this->sendResponse($coupon, "Coupon restore successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('coupons-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $coupon = $this->repository->permanentDelete($id);

            return $this->sendResponse($coupon, "Coupon permanently deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
