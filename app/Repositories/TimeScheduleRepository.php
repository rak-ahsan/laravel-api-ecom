<?php

namespace App\Repositories;

use Exception;
use Carbon\Carbon;
use App\Classes\Helper;
use App\Models\TimeSchedule;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class TimeScheduleRepository
{
    public function __construct(protected TimeSchedule $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $status       = $request->input('status', null);

        try {
            $timeSchedules = $this->model->with(["createdBy:id,username"])
            ->when($status, fn($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $timeSchedules;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $timeSchedule = new $this->model();

            $startTime = Carbon::parse($request->start_time);
            $endTime   = Carbon::parse($request->end_time);

            $timeSchedule->start_time = $startTime->format('H:i:s');
            $timeSchedule->end_time   = $endTime->format('H:i:s');
            $duration                 = $startTime->diff($endTime);
            $timeSchedule->duration   = $duration->format('%H:%I:%S');
            $timeSchedule->status     = $request->status;
            $timeSchedule->save();

            DB::commit();

            return $timeSchedule;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $timeSchedule = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$timeSchedule) {
                throw new CustomException("timeSchedule not found");
            }

            return $timeSchedule;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $timeSchedule = $this->model->find($id);
            if (!$timeSchedule) {
                throw new CustomException("Time schedule not found");
            }

            $startTime = Carbon::parse($request->start_time);
            $endTime   = Carbon::parse($request->end_time);

            $timeSchedule->start_time = $startTime->format('H:i:s');
            $timeSchedule->end_time   = $endTime->format('H:i:s');
            $duration                 = $startTime->diff($endTime);
            $timeSchedule->duration   = $duration->format('%H:%I:%S');
            $timeSchedule->status     = $request->status;
            $timeSchedule->save();

            DB::commit();

            return $timeSchedule;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $timeSchedule = $this->model->find($id);
            if (!$timeSchedule) {
                throw new CustomException("Time schedule not found");
            }

            return $timeSchedule->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
