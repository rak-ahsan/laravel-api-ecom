<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\OrderGuardRepository;
use App\Http\Requests\Admin\OrderGuardRequest;
use App\Http\Resources\Admin\OrderGuardResource;
use App\Http\Resources\Admin\OrderGuardCollection;

class OrderGuardController extends BaseController
{
    public function __construct(protected OrderGuardRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('order-guards-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $guards = $this->repository->index($request);

            $guards = new OrderGuardCollection($guards);

            return $this->sendResponse($guards, 'Order guard list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('order-guards-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $guard = $this->repository->show($id);

            $guard = new OrderGuardResource($guard);

            return $this->sendResponse($guard, 'Order guard single view', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(OrderGuardRequest $request)
    {
        if (!$request->user()->hasPermission('order-guards-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $guard = $this->repository->store($request);

            $guard = new OrderGuardResource($guard);

            return $this->sendResponse($guard, 'Order guard created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(OrderGuardRequest $request, $id)
    {
        if (!$request->user()->hasPermission('order-guards-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $guard = $this->repository->update($request, $id);

            $guard = new OrderGuardResource($guard);

            return $this->sendResponse($guard, 'Order guard updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('order-guards-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $guard = $this->repository->delete($id);

            return $this->sendResponse($guard, 'Order guard deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
