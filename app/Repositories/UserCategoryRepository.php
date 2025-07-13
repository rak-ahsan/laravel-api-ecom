<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Enums\StatusEnum;
use App\Models\UserCategory;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class UserCategoryRepository
{
    public function __construct(protected UserCategory $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $userCategories = $this->model->with(["createdBy:id,username"])
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("name", "like", "%$searchKey%")
                    ->orWhere("status", $searchKey);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $userCategories;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $userCategory = new $this->model();

            $userCategory->name   = $request->name;
            $userCategory->slug   = $request->name;
            $userCategory->status = $request->status ?? StatusEnum::ACTIVE->value;
            $userCategory->save();

            DB::commit();

            return $userCategory;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $userCategory = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$userCategory) {
                throw new CustomException("User category not found");
            }

            return $userCategory;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $userCategory = $this->model->find($id);
            if (!$userCategory) {
                throw new CustomException("User category Not found");
            }

            $userCategory->name   = $request->name;
            $userCategory->slug   = $request->name;
            $userCategory->status = $request->status ?? StatusEnum::ACTIVE->value;
            $userCategory->save();

            DB::commit();

            return $userCategory;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $userCategory = $this->model->find($id);
            if (!$userCategory) {
                throw new CustomException("User category not found");
            }

            return $userCategory->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $userCategories = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("name", "like", "%$searchKey%")
                    ->orWhere("status", $searchKey);
            })

            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $userCategories;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $userCategory = $this->model->onlyTrashed()->find($id);
            if (!$userCategory) {
                throw new CustomException("User category not found");
            }

            $userCategory->restore();

            return $userCategory;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $userCategory = $this->model->withTrashed()->find($id);
            if (!$userCategory) {
                throw new CustomException("User category not found");
            }

            return $userCategory->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
