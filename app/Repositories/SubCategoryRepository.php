<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Models\SubCategory;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class SubCategoryRepository
{
    public function __construct(protected SubCategory $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $sub_categories = $this->model->with(["category:id,name", "createdBy:id,username"])
            ->when($searchKey, function($query) use ($searchKey){
                $query->where("name", "like", "%$searchKey%")
                    ->orWhere("status", "like", "$searchKey");
            })
            ->orderBy('created_at', 'desc')->paginate($paginateSize);

            return $sub_categories;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $sub_categories = $this->model->with(["category","createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$sub_categories) {
                throw new CustomException("Sub Category not found");
            }

            return $sub_categories;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $subCategory = new $this->model();

            $subCategory->name        = $request->name;
            $subCategory->slug        = $request->name;
            $subCategory->category_id = $request->category_id;
            $subCategory->status      = $request->status;
            $subCategory->save();

            // Upload image
            if ($request->hasFile('image')) {
                Helper::uploadFile($subCategory, $request->image, $subCategory->uploadPath);
            }

            DB::commit();

            return $subCategory;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    function update($request,  $id)
    {
        try {
            DB::beginTransaction();

            $subCategory = $this->model->find($id);
            if (!$subCategory) {
                throw new CustomException("Sub Category not found");
            }

            $subCategory->name        = $request->name;
            $subCategory->slug        = $request->name;
            $subCategory->category_id = $request->category_id;
            $subCategory->status      = $request->status;
            $subCategory->save();

            // Upload image
            if ($request->hasFile('image')) {
                Helper::uploadFile($subCategory, $request->image, $subCategory->uploadPath, $subCategory->img_path);
            }

            DB::commit();

            return $subCategory;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $subCategory = $this->model->find($id);
            if (!$subCategory) {
                throw new CustomException("Sub Category not found");
            }

            // Delete old image
            if ($subCategory->img_path) {
                Helper::deleteFile($subCategory->img_path);
            }

            return $subCategory->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $subCategories = $this->model->with(["category:id,name", "createdBy:id,username"])
            ->onlyTrashed()
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("name", "like", "%$searchKey%")
                        ->orWhere("status", "like", "$searchKey");
            })
            ->orderBy('created_at', 'desc')->paginate($paginateSize);

            return $subCategories;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $subCategory = $this->model->onlyTrashed()->find($id);
            if (!$subCategory) {
                throw new CustomException("Sub Category not found");
            }
            $subCategory->restore();

            return $subCategory;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $subCategory = $this->model->withTrashed()->find($id);
            if (!$subCategory) {
                throw new CustomException("Sub Category not found");
            }

            return $subCategory->forceDelete();

        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
