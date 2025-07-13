<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\StatusRepository;
use App\Http\Requests\Admin\StatusRequest;
use App\Http\Resources\Admin\StatusResource;
use App\Http\Resources\Admin\StatusCollection;

class StatusController extends BaseController
{
    public function __construct(protected StatusRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('statuses-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $statuses = $this->repository->index($request);

            $statuses = new StatusCollection($statuses);

            return $this->sendResponse($statuses, 'Status list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(StatusRequest $request)
    {
        if (!$request->user()->hasPermission('statuses-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $status = $this->repository->store($request);

            $status = new StatusResource($status);

            return $this->sendResponse($status, 'Status created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('statuses-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $status = $this->repository->show($id);

            $status = new StatusResource($status);

            return $this->sendResponse($status, "Status single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(StatusRequest $request, $id)
    {
        if (!$request->user()->hasPermission('statuses-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $status = $this->repository->update($request, $id);

            $status = new StatusResource($status);

            return $this->sendResponse($status, 'Status updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('statuses-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $status = $this->repository->delete($id);

            return $this->sendResponse($status, 'Status deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('statuses-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $statuses = $this->repository->trashList($request);

            $statuses = new StatusCollection($statuses);

            return $this->sendResponse($statuses, 'Status trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('statuses-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $status = $this->repository->restore($id);

            $status = new StatusResource($status);

            return $this->sendResponse($status, "Status restore successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('statuses-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $status = $this->repository->permanentDelete($id);

            return $this->sendResponse($status, "Status permanently deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
