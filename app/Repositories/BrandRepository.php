<?php

namespace App\Repositories;

use Exception;
use App\Models\Brand;
use App\Classes\Helper;
use App\Enums\StatusEnum;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class BrandRepository
{
    public function __construct(protected Brand $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $brands = $this->model->with(["createdBy:id,username"])
            ->when($searchKey, function($query) use ($searchKey){
                $query->where("name", "like", "%$searchKey%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $brands;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $brand = new $this->model();

            $brand->name   = $request->name;
            $brand->slug   = $request->name;
            $brand->status = $request->status;
            $brand->save();

            // Upload image
            if ($request->hasFile('image')) {
                Helper::uploadFile($brand, $request->image, $brand->uploadPath);
            }

            DB::commit();

            return $brand;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $brand = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$brand) {
                throw new CustomException("Brand not found");
            }

            return $brand;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $brand = $this->model->find($id);
            if (!$brand) {
                throw new CustomException("Brand not found");
            }

            $brand->name   = $request->name;
            $brand->slug   = $request->name;
            $brand->status = $request->status ?? StatusEnum::ACTIVE->value;
            $brand->save();

            // Upload image
            if ($request->hasFile('image')) {
                Helper::uploadFile($brand, $request->image, $brand->uploadPath, $brand->img_path);
            }

            DB::commit();

            return $brand;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $brand = $this->model->find($id);
            if (!$brand) {
                throw new CustomException("Brand not found");
            }

            //  Delete old image
            if ($brand->img_path) {
                Helper::deleteFile($brand->img_path);
            }

            return $brand->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey = $request->input('search_key', null);

        try {
            $brands = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("name", "like", "%$searchKey%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $brands;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $brand = $this->model->onlyTrashed()->find($id);
            if (!$brand) {
                throw new CustomException("Brand not found");
            }
            $brand->restore();

            return $brand;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $brand = $this->model->withTrashed()->find($id);
            if (!$brand) {
                throw new CustomException("Brand not found");
            }

            return $brand->forceDelete();

        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
