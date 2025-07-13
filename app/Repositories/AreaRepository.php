<?php

namespace App\Repositories;

use Exception;
use App\Models\Area;
use App\Classes\Helper;
use App\Enums\StatusEnum;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class AreaRepository
{
    public function __construct(protected Area $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $isPaginate   = $request->input("is_paginate", null);
        $name         = $request->input("name", null);
        $status       = $request->input("status", null);

        try {
            $areas = $this->model->with(["createdBy:id,username"])
            ->when($name, fn ($query) => $query->where("name", "like", "%$name%"))
            ->when($status, fn ($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc');

            $areas = $isPaginate ? $areas->paginate($paginateSize) : $areas->get();

            return $areas;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $area = new $this->model();

            $area->name   = $request->name;
            $area->slug   = Str::slug($request->name);
            $area->status = $request->status ?? StatusEnum::ACTIVE->value;
            $area->save();

            DB::commit();

            return $area;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $area = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$area) {
                throw new CustomException("Area not found");
            }

            return $area;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $area = $this->model->find($id);

            if (!$area) {
                throw new CustomException("Area Not found");
            }

            $area->name   = $request->name;
            $area->slug   = Str::slug($request->name);
            $area->status = $request->status;
            $area->save();

            DB::commit();

            return $area;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $area = $this->model->find($id);
            if (!$area) {
                throw new CustomException("Area not found");
            }

            return $area->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $name         = $request->input("name", null);
        $status       = $request->input("status", null);

        try {
            $areas = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($name, fn($query) => $query->where("name", "like", "%$name%"))
            ->when($status, fn($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $areas;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $area = $this->model->onlyTrashed()->find($id);
            if (!$area) {
                throw new CustomException("Area not found");
            }

            $area->restore();

            return $area;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $area = $this->model->withTrashed()->find($id);
            if (!$area) {
                throw new CustomException("Area not found");
            }

            return $area->forceDelete();

        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
