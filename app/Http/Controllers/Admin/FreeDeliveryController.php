<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\FreeDeliveryRepository;
use App\Http\Requests\Admin\FreeDeliveryRequest;
use App\Http\Resources\Admin\FreeDeliveryResource;
use App\Http\Resources\Admin\FreeDeliveryCollection;

class FreeDeliveryController extends BaseController
{
    public function __construct(protected FreeDeliveryRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('free-delivery-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $freeDeliveries = $this->repository->index($request);

            $freeDeliveries = new FreeDeliveryCollection($freeDeliveries);

            return $this->sendResponse($freeDeliveries, 'Free delivery list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(FreeDeliveryRequest $request)
    {
        if (!$request->user()->hasPermission('free-delivery-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $freeDelivery = $this->repository->store($request);

            $freeDelivery = new FreeDeliveryResource($freeDelivery);

            return $this->sendResponse($freeDelivery, 'Free delivery created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('free-delivery-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $freeDelivery = $this->repository->show($id);

            $freeDelivery = new FreeDeliveryResource($freeDelivery);

            return $this->sendResponse($freeDelivery, 'Free delivery single view', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(FreeDeliveryRequest $request, $id)
    {
        if (!$request->user()->hasPermission('free-delivery-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $freeDelivery = $this->repository->update($request, $id);

            $freeDelivery = new FreeDeliveryResource($freeDelivery);

            return $this->sendResponse($freeDelivery, 'Free delivery updated successfully', 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('free-delivery-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $freeDelivery = $this->repository->delete($id);

            return $this->sendResponse($freeDelivery, 'Free Delivery deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('free-delivery-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $freeDeliveries = $this->repository->trashList($request);

            $freeDeliveries = new FreeDeliveryCollection($freeDeliveries);

            return $this->sendResponse($freeDeliveries, 'Free delivery trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('free-delivery-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $freeDelivery = $this->repository->restore($id);

            $freeDelivery = new FreeDeliveryResource($freeDelivery);

            return $this->sendResponse($freeDelivery, 'Free delivery restore successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('free-delivery-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $freeDelivery = $this->repository->permanentDelete($id);

            return $this->sendResponse($freeDelivery, 'Free delivery permanently deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
