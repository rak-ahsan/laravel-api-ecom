<?php

namespace App\Repositories;

use Exception;

use App\Classes\Helper;
use App\Models\Warranty;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class WarrantyRepository
{
    public function __construct(protected Warranty $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $warranties = $this->model->with(["createdBy:id,username"])
            ->when($searchKey, fn($query) => $query->where('name', 'like', "%$searchKey%"))
            ->orderBy('name', 'desc')->paginate($paginateSize);

            return $warranties;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try{
            DB::beginTransaction();

            $warranty = new $this->model();

            $warranty->name   = $request->name;
            $warranty->slug   = Str::slug($request->name);
            $warranty->status = $request->status;
            $warranty->days   = $request->days;
            $warranty->save();

            DB::commit();

            return $warranty;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $warranty = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$warranty) {
                throw new CustomException("Warranty not found");
            }

            return $warranty;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $warranty = $this->model->find($id);

            if (!$warranty) {
                throw new CustomException("Warranty not found");
            }

            $warranty->name   = $request->name;
            $warranty->slug   = Str::slug($request->name);
            $warranty->status = $request->status;
            $warranty->days   = $request->days;
            $warranty->save();

            DB::commit();

            return $warranty;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $warranty = $this->model->find($id);
            if (!$warranty) {
                throw new CustomException('Warranty not found');
            }

            return $warranty->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $warranties = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($searchKey, fn($query) => $query->where('name', 'like', "%$searchKey%"))
            ->orderBy('name', 'desc')->paginate($paginateSize);

            return $warranties;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $warranty = $this->model->onlyTrashed()->find($id);
            if (!$warranty) {
                throw new CustomException('Warranty not found');
            }

            $warranty->restore();

            return $warranty;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $warranty = $this->model->withTrashed()->find($id);
            if (!$warranty) {
                throw new CustomException('Warranty not found');
            }

            return $warranty->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
