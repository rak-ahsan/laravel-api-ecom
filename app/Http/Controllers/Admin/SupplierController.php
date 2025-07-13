<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\SupplierRepository;
use App\Http\Requests\Admin\SupplierRequest;
use App\Http\Resources\Admin\SupplierResource;
use App\Http\Resources\Admin\SupplierCollection;

class SupplierController extends BaseController
{
    public function __construct(protected SupplierRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('suppliers-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $suppliers = $this->repository->index($request);

            $suppliers = new SupplierCollection($suppliers);

            return $this->sendResponse($suppliers, 'Supplier list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(SupplierRequest $request)
    {
        if (!$request->user()->hasPermission('suppliers-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $supplier = $this->repository->store($request);

            $supplier = new SupplierResource($supplier);

            return $this->sendResponse($supplier, 'Supplier created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('suppliers-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $supplier = $this->repository->show($id);

            $supplier = new SupplierResource($supplier);

            return $this->sendResponse($supplier, 'Supplier single view', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(SupplierRequest $request, $id)
    {
        if (!$request->user()->hasPermission('suppliers-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $supplier = $this->repository->update($request, $id);

            $supplier = new SupplierResource($supplier);

            return $this->sendResponse($supplier, 'Supplier updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('suppliers-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $supplier = $this->repository->delete($id);

            return $this->sendResponse($supplier, 'Supplier deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('suppliers-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $suppliers = $this->repository->trashList($request);

            $suppliers = new SupplierCollection($suppliers);

            return $this->sendResponse($suppliers, 'Supplier trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('suppliers-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $supplier = $this->repository->restore($id);

            $supplier = new SupplierResource($supplier);

            return $this->sendResponse($supplier, 'Supplier restore successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('suppliers-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $supplier = $this->repository->permanentDelete($id);

            return $this->sendResponse($supplier, 'Supplier permanently deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
