<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\PurchaseRepository;
use App\Http\Requests\Admin\PurchaseRequest;
use App\Http\Resources\Admin\PurchaseResource;
use App\Http\Resources\Admin\PurchaseCollection;

class PurchaseController extends BaseController
{
    public function __construct(protected PurchaseRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission("purchases-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $purchases = $this->repository->index($request);

            $purchases = new PurchaseCollection($purchases);

            return $this->sendResponse($purchases, "Purchase information", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(PurchaseRequest $request)
    {
        if (!$request->user()->hasPermission("purchases-create")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $purchase = $this->repository->store($request);

            $purchase = new PurchaseResource($purchase);

            return $this->sendResponse($purchase, "Purchase created successfully", 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission("purchases-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $purchase = $this->repository->show($id);

            $purchase = new PurchaseResource($purchase);

            return $this->sendResponse($purchase, "Purchase details", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(PurchaseRequest $request, $id)
    {
        if (!$request->user()->hasPermission("purchases-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $purchase = $this->repository->update($request, $id);

            $purchase = new PurchaseResource($purchase);

            return $this->sendResponse($purchase, "Purchase updated successfully", 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
