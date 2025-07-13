<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Enums\StatusEnum;
use Illuminate\Support\Str;
use App\Models\SettingCategory;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class SettingCategoryRepository
{
    public function __construct(protected SettingCategory $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $name         = $request->input("name", null);
        $status       = $request->input("status", null);

        try {
            $settingCategories = $this->model->with(["createdBy:id,username"])
            ->when($name, fn($query) => $query->where("name", "like", "%$name%"))
            ->when($status, fn($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $settingCategories;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $settingCategory = new $this->model();

            $settingCategory->name   = $request->name;
            $settingCategory->slug   = Str::slug($request->name);
            $settingCategory->status = $request->status ?? StatusEnum::ACTIVE->value;
            $settingCategory->save();

            DB::commit();

            return $settingCategory;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $settingCategory = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$settingCategory) {
                throw new CustomException("Setting category not found");
            }

            return $settingCategory;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $SettingCategory = $this->model->find($id);

            if (!$SettingCategory) {
                throw new CustomException("Setting category not found");
            }

            $SettingCategory->name   = $request->name;
            $SettingCategory->slug   = Str::slug($request->name);
            $SettingCategory->status = $request->status;
            $SettingCategory->save();

            DB::commit();

            return $SettingCategory;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $settingCategory = $this->model->find($id);
            if (!$settingCategory) {
                throw new CustomException("Setting category not found");
            }

            return $settingCategory->delete();
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
            $settingCategories = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($name, fn($query) => $query->where("name", "like", "%$name%"))
            ->when($status, fn($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $settingCategories;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $settingCategory = $this->model->onlyTrashed()->find($id);
            if (!$settingCategory) {
                throw new CustomException("setting category not found");
            }

            $settingCategory->restore();

            return $settingCategory;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $settingCategory = $this->model->withTrashed()->find($id);
            if (!$settingCategory) {
                throw new CustomException("setting category not found");
            }

            return $settingCategory->forceDelete();

        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
