<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\TimeScheduleRepository;
use App\Http\Requests\Admin\TimeScheduleRequest;
use App\Http\Resources\Admin\TimeScheduleResource;
use App\Http\Resources\Admin\TimeScheduleCollection;

class TimeScheduleController extends BaseController
{
    public function __construct(protected TimeScheduleRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('time-schedules-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $timeSchedules = $this->repository->index($request);

            $timeSchedules = new TimeScheduleCollection($timeSchedules);

            return $this->sendResponse($timeSchedules, 'Time schedule list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(TimeScheduleRequest $request)
    {
        if (!$request->user()->hasPermission('time-schedules-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $timeSchedule = $this->repository->store($request);

            $timeSchedule = new TimeScheduleResource($timeSchedule);

            return $this->sendResponse($timeSchedule, 'Time schedule created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('time-schedules-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $timeSchedule = $this->repository->show($id);

            $timeSchedule = new TimeScheduleResource($timeSchedule);

            return $this->sendResponse($timeSchedule, 'Time schedule single view', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(TimeScheduleRequest $request, $id)
    {
        if (!$request->user()->hasPermission('time-schedules-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $timeSchedule = $this->repository->update($request, $id);

            $timeSchedule = new TimeScheduleResource($timeSchedule);

            return $this->sendResponse($timeSchedule, 'Time schedule updated successfully', 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('time-schedules-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $timeSchedule = $this->repository->delete($id);

            return $this->sendResponse($timeSchedule, 'Time schedule deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
