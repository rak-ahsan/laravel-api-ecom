<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\OrderFromRepository;
use App\Http\Requests\Admin\OrderFromRequest;
use App\Http\Resources\Admin\OrderFromResource;
use App\Http\Resources\Admin\OrderFromCollection;

class OrderFromController extends BaseController
{
    public function __construct(protected OrderFromRepository $repository){}

    public function index(Request $request){

        if (!$request->user()->hasPermission('order-froms-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orderFrom = $this->repository->index($request);

            $orderFrom = new OrderFromCollection($orderFrom);

            return $this->sendResponse($orderFrom, "Order from List", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("Common.commonError"));
        }
    }

    public function store(OrderFromRequest $request)
    {
        if (!$request->user()->hasPermission('order-froms-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orderFrom = $this->repository->store($request);

            $orderFrom = new OrderFromResource($orderFrom);

            return $this->sendResponse($orderFrom, 'Order from created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('order-froms-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $orderFrom = $this->repository->show($id);

            $orderFrom = new OrderFromResource($orderFrom);

            return $this->sendResponse($orderFrom, "Order from single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function update(OrderFromRequest $request, $id)
    {
        if (!$request->user()->hasPermission('order-froms-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orderFrom = $this->repository->update($request, $id);

            $orderFrom = new OrderFromResource($orderFrom);

            return $this->sendResponse($orderFrom, 'Order from updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('order-froms-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $category = $this->repository->delete($id);

            return $this->sendResponse($category, 'Order from deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('order-froms-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orderFrom = $this->repository->trashList($request);

            $orderFrom = new OrderFromCollection($orderFrom);

            return $this->sendResponse($orderFrom, "Order form trash List", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("Common.commonError"));
        }
    }

    function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('order-froms-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orderFrom = $this->repository->restore($id);

            $orderFrom = new OrderFromResource($orderFrom);

            return $this->sendResponse($orderFrom, 'Order from restore successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('order-froms-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orderFrom = $this->repository->permanentDelete($id);

            return $this->sendResponse($orderFrom, 'Order from permanent delete successfully', 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
