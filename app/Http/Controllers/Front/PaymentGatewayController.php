<?php

namespace App\Http\Controllers\Front;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\PaymentGatewayRepository;
use App\Http\Resources\Front\PaymentGatewayResource;
use App\Http\Resources\Front\PaymentGatewayCollection;

class PaymentGatewayController extends BaseController
{
    protected $repository;

    public function __construct(PaymentGatewayRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE->value]);

            $paymentGateways = $this->repository->index($request);

            $paymentGateways = new PaymentGatewayCollection($paymentGateways);

            return $this->sendResponse($paymentGateways, 'Payment gateway list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE->value]);

            $paymentGateway = $this->repository->show($id);

            $paymentGateway = new PaymentGatewayResource($paymentGateway);

            return $this->sendResponse($paymentGateway, "PaymentGateway single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
