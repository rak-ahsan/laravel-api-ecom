<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Classes\BaseController;
use Illuminate\Support\Facades\Log;
use App\Repositories\RedxRepository;
use App\Http\Requests\Admin\RedxGetAreaRequest;
use App\Http\Requests\Admin\RedxCreateStoreRequest;
use App\Http\Requests\Admin\RedxParcelCreateRequest;
use App\Http\Requests\Admin\RedxEnvCredentialRequest;
use App\Http\Resources\Admin\RedxEnvCredentialResource;

class RedxController extends BaseController
{
    public function __construct(protected RedxRepository $repository){}

    public function getArea(RedxGetAreaRequest $request)
    {
        try {
            $result = $this->repository->getArea($request);

            return $this->sendResponse($result, "Redx area list");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function createPickupStore(RedxCreateStoreRequest $request)
    {
        try {
            $result = $this->repository->createPickupStore($request);

            return $this->sendResponse($result, "Store created successfully");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function getPickupStore()
    {
        try {
            $result = $this->repository->getPickupStore();

            return $this->sendResponse($result, "Store list");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function getPickupStoreDetail($id)
    {
        try {
            $result = $this->repository->getPickupStoreDetail($id);

            return $this->sendResponse($result, "Store details");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function parcelTrack($id)
    {
        try {
            $result = $this->repository->parcelTrack($id);

            return $this->sendResponse($result, "Track parcel");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function parcelCreate(RedxParcelCreateRequest $request)
    {
        try {
            $result = $this->repository->parcelCreate($request);

            return $this->sendResponse($result, "Parcel created successfully");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function parcelDetail($id)
    {
        try {
            $result = $this->repository->parcelDetail($id);

            return $this->sendResponse($result, "Parcel details");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function updateEnvCredential(RedxEnvCredentialRequest $request)
    {
        if (!$request->user()->hasPermission('redx-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $res = $this->repository->updateEnvCredential($request);

            $res = new RedxEnvCredentialResource($res);

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

            $res = new RedxEnvCredentialResource($res);

            return $this->sendResponse($res, "Redx credentials");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
