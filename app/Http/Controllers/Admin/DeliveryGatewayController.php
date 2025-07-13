<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\DeliveryGatewayRepository;
use App\Http\Requests\Admin\DeliveryGatewayRequest;
use App\Http\Resources\Admin\DeliveryGatewayResource;
use App\Http\Resources\Admin\DeliveryGatewayCollection;

class DeliveryGatewayController extends BaseController
{
    public function __construct(protected DeliveryGatewayRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('delivery-gateways-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $deliveryGateways = $this->repository->index($request);

            $deliveryGateways = new DeliveryGatewayCollection($deliveryGateways);

            return $this->sendResponse($deliveryGateways, 'Delivery gateway list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(DeliveryGatewayRequest $request)
    {
        if (!$request->user()->hasPermission('delivery-gateways-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $deliveryGateway = $this->repository->store($request);

            $deliveryGateway = new DeliveryGatewayResource($deliveryGateway);

            return $this->sendResponse($deliveryGateway, 'Delivery gateway created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('delivery-gateways-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $deliveryGateway = $this->repository->show($id);

            $deliveryGateway = new DeliveryGatewayResource($deliveryGateway);

            return $this->sendResponse($deliveryGateway, "Delivery gateway single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(DeliveryGatewayRequest $request, $id)
    {
        if (!$request->user()->hasPermission('delivery-gateways-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $deliveryGateway = $this->repository->update($request, $id);

            $deliveryGateway = new DeliveryGatewayResource($deliveryGateway);

            return $this->sendResponse($deliveryGateway, 'Delivery gateway updated successfully', 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            info($exception);

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('delivery-gateways-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $deliveryGateway = $this->repository->delete($id);

            return $this->sendResponse($deliveryGateway, 'Delivery gateway deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('delivery-gateways-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $deliveryGateways = $this->repository->trashList($request);

            $deliveryGateways = new DeliveryGatewayCollection($deliveryGateways);

            return $this->sendResponse($deliveryGateways, 'Delivery gateway trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('delivery-gateways-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $deliveryGateway = $this->repository->restore($id);

            $deliveryGateway = new DeliveryGatewayResource($deliveryGateway);

            return $this->sendResponse($deliveryGateway, "Delivery gateway restore successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('delivery-gateways-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $deliveryGateway = $this->repository->permanentDelete($id);

            return $this->sendResponse($deliveryGateway, "Delivery gateway permanently deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
