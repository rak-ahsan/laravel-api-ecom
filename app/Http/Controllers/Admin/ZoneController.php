<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\ZoneRepository;
use App\Http\Requests\Admin\ZoneRequest;
use App\Http\Resources\Admin\ZoneResource;
use App\Http\Resources\Admin\ZoneCollection;

class ZoneController extends BaseController
{
    public function __construct(protected ZoneRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('zones-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $zones = $this->repository->index($request);

            $zones = new ZoneCollection($zones);

            return $this->sendResponse($zones, 'Zone list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(ZoneRequest $request)
    {
        if (!$request->user()->hasPermission('zones-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $zone = $this->repository->store($request);

            $zone = new ZoneResource($zone);

            return $this->sendResponse($zone, 'Zone created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('zones-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $zone = $this->repository->show($id);

            $zone = new ZoneResource($zone);

            return $this->sendResponse($zone, "Zone single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(ZoneRequest $request, $id)
    {
        if (!$request->user()->hasPermission('zones-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $zone = $this->repository->update($request, $id);

            $zone = new ZoneResource($zone);

            return $this->sendResponse($zone, 'Zone updated successfully', 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('zones-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $zone = $this->repository->delete($id);

            return $this->sendResponse($zone, 'Zone deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('zones-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $zones = $this->repository->trashList($request);

            $zones = new ZoneCollection($zones);

            return $this->sendResponse($zones, 'Zone trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('zones-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $zone = $this->repository->restore($id);

            $zone = new ZoneResource($zone);

            return $this->sendResponse($zone, "Zone restore successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('zones-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $zone = $this->repository->permanentDelete($id);

            return $this->sendResponse($zone, "Zone permanently deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
