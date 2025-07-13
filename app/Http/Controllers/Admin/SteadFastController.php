<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\SteadFastRepository;
use App\Http\Requests\Admin\SteadFastCreateOrderRequest;
use App\Http\Resources\Admin\SteadFastEnvCredentialResource;
use App\Http\Requests\Admin\SteadFastUpdateEnvCredentialRequest;

class SteadFastController extends BaseController
{
    public function __construct(protected SteadFastRepository $repository){}

    public function createOrder(SteadFastCreateOrderRequest $request)
    {
        try {
            $result = $this->repository->createOrder($request);

            return $this->sendResponse($result, $result["message"]);

        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function getDeliveryStatus($id)
    {
        try {
            $result = $this->repository->getDeliveryStatus($id);

            return $this->sendResponse($result, "Order delivery status");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function getCurrentBalance()
    {
        try {
            $result = $this->repository->getCurrentBalance();

            return $this->sendResponse($result, "Balance");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function updateEnvCredential(SteadFastUpdateEnvCredentialRequest $request)
    {
        if (!$request->user()->hasPermission('stead-fast-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $res = $this->repository->updateEnvCredential($request);

            $res = new SteadFastEnvCredentialResource($res);

            return $this->sendResponse($res, 'Credential updated successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show()
    {
        try {
            $res = $this->repository->show();

            $res = new SteadFastEnvCredentialResource($res);

            return $this->sendResponse($res, "Stead fast credentials");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
