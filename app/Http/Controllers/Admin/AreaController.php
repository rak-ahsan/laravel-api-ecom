<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\AreaRepository;
use App\Http\Requests\Admin\AreaRequest;
use App\Http\Resources\Admin\AreaResource;
use App\Http\Resources\Admin\AreaCollection;

class AreaController extends BaseController
{
    public function __construct(protected AreaRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('areas-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $areas = $this->repository->index($request);

            $areas = new AreaCollection($areas);

            return $this->sendResponse($areas, 'Area list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(AreaRequest $request)
    {
        if (!$request->user()->hasPermission('areas-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $area = $this->repository->store($request);

            $area = new AreaResource($area);

            return $this->sendResponse($area, 'Area created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('areas-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $area = $this->repository->show($id);

            $area = new AreaResource($area);

            return $this->sendResponse($area, "Area single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(AreaRequest $request, $id)
    {
        if (!$request->user()->hasPermission('areas-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $area = $this->repository->update($request, $id);

            $area = new AreaResource($area);

            return $this->sendResponse($area, 'Area updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('areas-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $area = $this->repository->delete($id);

            return $this->sendResponse($area, 'Area deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('areas-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $areas = $this->repository->trashList($request);

            $areas = new AreaCollection($areas);

            return $this->sendResponse($areas, 'Area trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('areas-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $area = $this->repository->restore($id);

            $area = new AreaResource($area);

            return $this->sendResponse($area, 'Area restore successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('areas-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $area = $this->repository->permanentDelete($id);

            return $this->sendResponse($area, 'Area permanently deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
