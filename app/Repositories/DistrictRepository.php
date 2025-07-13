<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Models\District;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class DistrictRepository
{
    public function __construct(protected District $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $isPaginate   = $request->input('is_paginate', true);
        $name         = $request->input('name', null);
        $slug         = $request->input("slug", null);

        try {
            $districts = $this->model->with(["createdBy:id,username"])
            ->when($name, fn ($query) => $query->where("name", "like", "%$name%"))
            ->when($slug, fn ($query) => $query->where("slug", $slug));

            if ($isPaginate) {
                $districts = $this->model->orderBy('created_at', 'desc')->whereNotIn('id', [1])->paginate($paginateSize);
            } else {
                $districts = $this->model->select('id', 'name')->orderBy('name', 'asc')->whereNotIn('id', [1])->get();
            }

            return $districts;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $district = new $this->model();

            $district->name = $request->name;
            $district->slug = Str::slug($request->name);
            $district->save();

            DB::commit();

            return $district;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $district = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$district) {
                throw new CustomException("District not found");
            }

            return $district;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $district = $this->model->find($id);
            if (!$district) {
                throw new CustomException("District not found");
            }

            $district->name   = $request->name;
            $district->slug   = Str::slug($request->name);
            $district->save();

            DB::commit();

            return $district;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $district = $this->model->find($id);
            if (!$district) {
                throw new CustomException("District not found");
            }

            return $district->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $name         = $request->input('name', null);
        $slug         = $request->input("slug", null);

        try {
            $districts = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($name, fn($query) => $query->where("name", "like", "%$name%"))
            ->when($slug, fn($query) => $query->where("slug", $slug))
            ->paginate($paginateSize);

            return $districts;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $district = $this->model->onlyTrashed()->find($id);
            if (!$district) {
                throw new CustomException("District not found");
            }

            $district->restore();

            return $district;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $district = $this->model->withTrashed()->find($id);
            if (!$district) {
                throw new CustomException("District not found");
            }

            return $district->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
