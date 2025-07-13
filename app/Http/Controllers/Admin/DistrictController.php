<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\DistrictRepository;
use App\Http\Requests\Admin\DistrictRequest;
use App\Http\Resources\Admin\DistrictResource;
use App\Http\Resources\Admin\DistrictCollection;

class DistrictController extends BaseController
{
    public function __construct(protected DistrictRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('districts-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $districts = $this->repository->index($request);

            $districts = new DistrictCollection($districts);

            return $this->sendResponse($districts, 'District list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(DistrictRequest $request)
    {
        if (!$request->user()->hasPermission('districts-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $district = $this->repository->store($request);

            $district = new DistrictResource($district);

            return $this->sendResponse($district, 'District created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('districts-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $district = $this->repository->show($id);

            $district = new DistrictResource($district);

            return $this->sendResponse($district, "District single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(DistrictRequest $request, $id)
    {
        if (!$request->user()->hasPermission('districts-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $district = $this->repository->update($request, $id);

            $district = new DistrictResource($district);

            return $this->sendResponse($district, 'District updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('districts-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $district = $this->repository->delete($id);

            return $this->sendResponse($district, 'District deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('districts-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $districts = $this->repository->trashList($request);

            $districts = new DistrictCollection($districts);

            return $this->sendResponse($districts, 'District trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('districts-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $district = $this->repository->restore($id);

            $district = new DistrictResource($district);

            return $this->sendResponse($district, "District restore successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('districts-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $district = $this->repository->permanentDelete($id);

            return $this->sendResponse($district, "District permanently deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
