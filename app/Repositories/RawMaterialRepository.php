<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Models\RawMaterial;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class RawMaterialRepository
{
    public function __construct(protected RawMaterial $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $isPaginate   = $request->input('is_paginate', true);
        $searchKey    = $request->input('search_key', null);

        try {
            $materials = $this->model->with(["createdBy:id,username"])
            ->when($searchKey, fn($query) => $query->where("name", "like", "%$searchKey%"))
            ->orderBy('created_at', 'desc');

            if ($isPaginate) {
                $materials = $materials->orderBy('created_at', 'desc')->paginate($paginateSize);
            } else {
                $materials = $materials->orderBy('name', 'asc')->get();
            }

            return $materials;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $material = new $this->model();

            $total = $request->unit_cost * $request->quantity;

            $material->name      = $request->name;
            $material->slug      = $request->name;
            $material->unit_cost = $request->unit_cost;
            $material->quantity  = $request->quantity;
            $material->total     = $total;
            $material->save();

            if ($request->hasFile('image')) {
                Helper::uploadFile($material, $request->image, $material->uploadPath);
            }

            DB::commit();

            return $material;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $material = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$material) {
                throw new CustomException("Raw Material not found");
            }

            return $material;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $material = $this->model->find($id);
            if (!$material) {
                throw new CustomException("Raw Material not found");
            }

            $total = $request->unit_cost * $request->quantity;

            $material->name      = $request->name;
            $material->slug      = $request->name;
            $material->unit_cost = $request->unit_cost;
            $material->quantity  = $request->quantity;
            $material->total     = $total;
            $material->save();

            // Upload image
            if ($request->hasFile('image')) {
                Helper::uploadFile($material, $request->image, $material->uploadPath, $material->img_path);
            }

            DB::commit();

            return $material;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $material = $this->model->find($id);
            if (!$material) {
                throw new CustomException("Raw Material not found");
            }

            //  Delete old image
            if ($material->img_path) {
                Helper::deleteFile($material->img_path);
            }

            return $material->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $isPaginate   = $request->input('is_paginate', true);
        $searchKey    = $request->input('search_key', null);

        try {
            $materials = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($searchKey, fn($query) => $query->where("name", "like", "%$searchKey%"))
            ->orderBy('created_at', 'desc');

            if ($isPaginate) {
                $materials = $materials->orderBy('created_at', 'desc')->paginate($paginateSize);
            } else {
                $materials = $materials->orderBy('name', 'asc')->get();
            }

            return $materials;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $material = $this->model->onlyTrashed()->find($id);
            if (!$material) {
                throw new CustomException("Raw Material not found");
            }

            $material->restore();

            return $material;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $material = $this->model->withTrashed()->find($id);
            if (!$material) {
                throw new CustomException("Raw Material not found");
            }

            return $material->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
