<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Models\Category;
use App\Enums\StatusEnum;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class CategoryRepository
{
    public function __construct(protected Category $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $categories = $this->model->with(["createdBy:id,username", "subCategory:id,name,slug"])
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("name", "like", "%$searchKey%")
                    ->orWhere("status", $searchKey);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $categories;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $category = new $this->model();

            $category->name   = $request->name;
            $category->slug   = $request->name;
            $category->status = $request->status ?? StatusEnum::ACTIVE->value;
            $category->save();

            // Upload image
            if ($request->hasFile('image')) {
                Helper::uploadFile($category, $request->image, $category->uploadPath);
            }

            DB::commit();

            return $category;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $category = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$category) {
                throw new CustomException("Category not found");
            }

            return $category;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $category = Category::find($id);
            if (!$category) {
                throw new CustomException("Category Not found");
            }

            $category->name   = $request->name;
            $category->slug   = $request->name;
            $category->status = $request->status ?? StatusEnum::ACTIVE->value;
            $category->save();

            if ($request->hasFile('image')) {
                Helper::uploadFile($category, $request->image, $category->uploadPath, $category->img_path);
            }

            DB::commit();

            return $category;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $category = $this->model->find($id);
            if (!$category) {
                throw new CustomException("Category not found");
            }

            // Delete old image
            if ($category->img_path) {
                Helper::deleteFile($category->img_path);
            }

            return $category->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey = $request->input('search_key', null);

        try {
            $categories = $this->model->with(["createdBy:id,username", "subCategory:id,name,slug"])
            ->onlyTrashed()
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("name", "like", "%$searchKey%")
                    ->orWhere("status", $searchKey);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $categories;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $category = $this->model->onlyTrashed()->find($id);
            if (!$category) {
                throw new CustomException("Category not found");
            }

            $category->restore();

            return $category;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $category = $this->model->withTrashed()->find($id);
            if (!$category) {
                throw new CustomException("Category not found");
            }

            return $category->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
