<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\OrderRepository;
use App\Http\Resources\Admin\OrderResource;
use App\Http\Resources\Admin\OrderCollection;
use App\Http\Requests\Admin\StoreOrderRequest;
use App\Http\Requests\Admin\OrderPreparedRequest;
use App\Http\Requests\Admin\AddRawMaterialRequest;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Http\Requests\Admin\StoreAdditionalCostRequest;
use App\Http\Requests\Admin\UpdateOrderPaidStatusRequest;

class OrderController extends BaseController
{
    public function __construct(protected OrderRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('orders-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders = $this->repository->index($request);

            $orders = new OrderCollection($orders);

            return $this->sendResponse($orders, "Order list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(StoreOrderRequest $request)
    {
        if (!$request->user()->hasPermission('orders-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->store($request);

            $order = new OrderResource($order);

            return $this->sendResponse($order, 'Order submitted successfully', 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('orders-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->show($id);

            $order = new OrderResource($order);

            return $this->sendResponse($order, "Order single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(Request $request, $id)
    {
        if (!$request->user()->hasPermission('orders-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->update($request, $id);

            $order = new OrderResource($order);

            return $this->sendResponse($order, 'Order updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('orders-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->destroy($id);

            return $this->sendResponse($order, 'Order Deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function updateStatus(UpdateOrderStatusRequest $request)
    {
        if (!$request->user()->hasPermission('orders-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->updateStatus($request);

            return $this->sendResponse($order, 'Status updated successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function updatePaidStatus(UpdateOrderPaidStatusRequest $request)
    {
        if (!$request->user()->hasPermission('orders-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->updatePaidStatus($request);

            return $this->sendResponse($order, 'Paid status updated successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function addAdditionCost(StoreAdditionalCostRequest $request)
    {
        if (!$request->user()->hasPermission('orders-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->addAdditionCost($request);

            return $this->sendResponse($order, 'Cost added updated successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function addRawMaterial(AddRawMaterialRequest $request)
    {
        if (!$request->user()->hasPermission('orders-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->addRawMaterial($request);

            return $this->sendResponse($order, 'Raw material added successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function multipleInvoice(Request $request)
    {
        if (!$request->user()->hasPermission('orders-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders = $this->repository->multipleInvoice($request);

            $orders = new OrderCollection($orders);

            return $this->sendResponse($orders, "Multiple invoice", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderTeamList(Request $request)
    {
        if (!$request->user()->hasPermission('orders-team-list-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders = $this->repository->orderTeamList($request);

            $orders = new OrderCollection($orders);

            return $this->sendResponse($orders, "Order team list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function preparedBy(OrderPreparedRequest $request)
    {
        if (!$request->user()->hasPermission('orders-prepare-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->preparedBy($request);

            return $this->sendResponse($order, "Assign successfully done", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function preparedByRestore(OrderPreparedRequest $request)
    {
        if (!$request->user()->hasPermission('orders-prepare-restore-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->preparedByRestore($request);

            return $this->sendResponse($order, "Restore successfully done", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function preparedByList(Request $request)
    {
        if (!$request->user()->hasPermission('orders-team-list-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders = $this->repository->preparedByList($request);

            $orders = new OrderCollection($orders);

            return $this->sendResponse($orders, "Order prepared by list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderHistory(Request $request, $id)
    {
        try {
            $orderHistories = $this->repository->orderHistory($request, $id);

            return $this->sendResponse($orderHistories, "Order history", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderLockedStatus($id)
    {
        try {
            $lockStatus = $this->repository->orderLockedStatus($id);

            return $this->sendResponse($lockStatus, "Order locked by", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderLocked($id)
    {
        try {
            $lockOrder = $this->repository->orderLocked($id);

            return $this->sendResponse($lockOrder, "Order locked", 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('orders-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders = $this->repository->trashList($request);

            $orders = new OrderCollection($orders);

            return $this->sendResponse($orders, "Order trash list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('orders-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->restore($id);

            $order = new OrderResource($order);

            return $this->sendResponse($order, 'Order restore successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('orders-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->permanentDelete($id);

            return $this->sendResponse($order, 'Order permanent delete successfully', 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
