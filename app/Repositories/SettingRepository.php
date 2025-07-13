<?php

namespace App\Repositories;

use Exception;
use App\Models\Setting;
use App\Classes\Helper;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class SettingRepository
{
    public function __construct(protected Setting $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $key          = $request->input('key', null);
        $status       = $request->input("status", null);

        try {
            $settings = $this->model->with(["settingCategory:id,name", "createdBy:id,username"])
            ->when($key, fn ($query) => $query->where('key', $key))
            ->when($status, fn ($query) => $query->where("status", $status))
            ->paginate($paginateSize);

            return $settings;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $setting = new $this->model();

            $setting->key                 = $request->key;
            $setting->value               = $request->value;
            $setting->status              = $request->status;
            $setting->setting_category_id = $request->setting_category_id;
            $setting->save();

            // Upload image
            if ($request->hasFile('image')) {
                Helper::uploadFile($setting, $request->image, $setting->uploadPath);
            }

            DB::commit();

            return $setting;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $setting = $this->model->with(["settingCategory:id,name", "createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$setting) {
                throw new CustomException('Setting Not found');
            }

            return $setting;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $setting = $this->model->find($id);
            if (!$setting) {
                throw new CustomException("Setting Not found");
            }

            $setting->key                 = $request->key;
            $setting->value               = $request->value;
            $setting->status              = $request->status;
            $setting->setting_category_id = $request->setting_category_id;
            $setting->save();

            // Upload image
            if ($request->hasFile('image')) {
                Helper::uploadFile($setting, $request->image, $setting->uploadPath, $setting->img_path);
            }

            DB::commit();

            return $setting;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $setting = $this->model->find($id);
            if (!$setting) {
                throw new CustomException("Setting not found");
            }

            //  Delete old image
            if ($setting->img_path) {
                Helper::deleteFile($setting->img_path);
            }

            return $setting->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $isPaginate   = $request->input('is_paginate', true);
        $key          = $request->input('key', null);
        $status       = $request->input("status", null);

        try {
            $settings = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($key, fn($query) => $query->where('key', $key))
            ->when($status, fn($query) => $query->where("status", $status))
            ->paginate($paginateSize);

            return $settings;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $setting = $this->model->onlyTrashed()->find($id);
            if (!$setting) {
                throw new CustomException("Setting not found");
            }

            $setting->restore();

            return $setting;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $setting = $this->model->withTrashed()->find($id);
            if (!$setting) {
                throw new CustomException("Setting not found");
            }

            return $setting->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
