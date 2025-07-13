<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\RawMaterialRepository;
use App\Http\Requests\Admin\RawMaterialRequest;
use App\Http\Resources\Admin\RawMaterialResource;
use App\Http\Resources\Admin\RawMaterialCollection;

class RawMaterialController extends BaseController
{
    public function __construct(protected RawMaterialRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('raw-materials-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $materials = $this->repository->index($request);

            $materials = new RawMaterialCollection($materials);

            return $this->sendResponse($materials, 'Raw material list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(RawMaterialRequest $request)
    {
        if (!$request->user()->hasPermission('raw-materials-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $material = $this->repository->store($request);

            $material = new RawMaterialResource($material);

            return $this->sendResponse($material, 'Raw material created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('raw-materials-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $material = $this->repository->show($id);

            $material = new RawMaterialResource($material);

            return $this->sendResponse($material, 'Raw material single view', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(RawMaterialRequest $request, $id)
    {
        if (!$request->user()->hasPermission('raw-materials-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $material = $this->repository->update($request, $id);

            $material = new RawMaterialResource($material);

            return $this->sendResponse($material, 'Raw material updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('raw-materials-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $material = $this->repository->delete($id);

            return $this->sendResponse($material, 'Raw material deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('raw-materials-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $materials = $this->repository->trashList($request);

            $materials = new RawMaterialCollection($materials);

            return $this->sendResponse($materials, 'Raw material trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('raw-materials-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $material = $this->repository->restore($id);

            $material = new RawMaterialResource($material);

            return $this->sendResponse($material, 'Raw material restore successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('raw-materials-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $material = $this->repository->permanentDelete($id);

            return $this->sendResponse($material, 'Raw material permanently deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
