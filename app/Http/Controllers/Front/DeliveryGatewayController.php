<?php

namespace App\Http\Controllers\Front;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Models\DeliveryGateway;
use Illuminate\Support\Facades\Log;
use App\Repositories\DeliveryGatewayRepository;
use App\Http\Resources\Front\DeliveryGatewayCollection;

class DeliveryGatewayController extends BaseController
{
    protected $repository;

    public function __construct(DeliveryGatewayRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE->value]);

            $deliveryGateway = $this->repository->index($request);

            $deliveryGateway = new DeliveryGatewayCollection($deliveryGateway);

            return $this->sendResponse($deliveryGateway, 'Delivery gateway list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function deliveryPrice($id)
    {
        try {
            $deliveryGateway = DeliveryGateway::select('delivery_fee')->find($id);

            return $this->sendResponse($deliveryGateway, 'Delivery charge', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
