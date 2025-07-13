<?php

namespace App\Repositories;

use Exception;
use App\Models\Status;
use App\Classes\Helper;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class StatusRepository
{
    public function __construct(protected Status $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey = $request->input('search_key', null);

        try {
            $statuses = $this->model->withCount(["orders"])
            ->when($searchKey, fn($query) => $query->where("name", "like", "%$searchKey%"))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $statuses;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $status = new $this->model();

            $status->name       = $request->name;
            $status->slug       = $request->name;
            $status->bg_color   = $request->bg_color;
            $status->text_color = $request->text_color;
            $status->status     = $request->statuses;
            $status->save();

            DB::commit();

            return $status;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $status = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$status) {
                throw new CustomException("Status not found");
            }

            return $status;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $status = $this->model->find($id);
            if (!$status) {
                throw new CustomException("Status Not found");
            }

            $status->name       = $request->name;
            $status->slug       = $request->name;
            $status->text_color = $request->text_color;
            $status->bg_color   = $request->bg_color;
            $status->status     = $request->status;
            $status->save();

            DB::commit();

            return $status;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $status = $this->model->find($id);
            if (!$status) {
                throw new CustomException("Status not found");
            }

            return $status->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $statuses = $this->model->withCount(["orders"])
            ->onlyTrashed()
            ->when($searchKey, fn($query) => $query->where("title", "like", "%$searchKey%"))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $statuses;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $status = $this->model->onlyTrashed()->find($id);
            if (!$status) {
                throw new CustomException("Status not found");
            }

            $status->restore();

            return $status;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $status = $this->model->withTrashed()->find($id);
            if (!$status) {
                throw new CustomException("Status not found");
            }

            return $status->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
