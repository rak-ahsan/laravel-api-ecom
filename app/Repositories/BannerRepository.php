<?php

namespace App\Repositories;

use Exception;
use App\Models\Banner;
use App\Classes\Helper;
use App\Enums\StatusEnum;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class BannerRepository
{
    public function __construct(protected Banner $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $isPaginate   = $request->input('is_paginate', true);
        $title        = $request->input('title', null);
        $status       = $request->input("status", null);

        try {
            $banners = $this->model->with(["createdBy:id,username"])
                ->when($title, fn ($query) => $query->where("title", "like", "%$title%"))
                ->when($status, fn ($query) => $query->where("status", $status));

            if ($isPaginate) {
                $banners = $banners->orderBy('created_at', 'desc')->paginate($paginateSize);
            } else {
                $banners = $banners->orderBy('title', 'asc')->get();
            }

            return $banners;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $banner = new $this->model();

            $banner->title       = $request->title;
            $banner->type        = $request->type;
            $banner->link        = $request->link;
            $banner->status      = $request->status;
            $banner->description = $request->description;
            $banner->save();

            // Upload image
            if ($request->hasFile('image')) {
                Helper::uploadFile($banner, $request->image, $banner->uploadPath);
            }

            DB::commit();

            return $banner;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id, $status = null)
    {
        try {
            $banner = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])
            ->when($status, fn ($query) => $query->where("status", $status))
            ->find($id);

            if (!$banner) {
                throw new CustomException("Banner not found");
            }
            return $banner;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $banner = $this->model->find($id);
            if (!$banner) {
                throw new CustomException("Banner Not found");
            }

            $banner->title       = $request->title;
            $banner->type        = $request->type;
            $banner->link        = $request->link;
            $banner->status      = $request->status ?? StatusEnum::ACTIVE->value;
            $banner->description = $request->description;
            $banner->save();

            // Update image
            if ($request->hasFile('image')) {
                Helper::uploadFile($banner, $request->image, $banner->uploadPath, $banner->img_path);
            }

            DB::commit();

            return $banner;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $banner = $this->model->find($id);
            if (!$banner) {
                throw new CustomException("Banner not found");
            }

            //  Delete old image
            if ($banner->img_path) {
                Helper::deleteFile($banner->img_path);
            }

            return $banner->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $isPaginate   = $request->input('is_paginate', true);
        $title        = $request->input('title', null);
        $status       = $request->input("status", null);

        try {
            $banners = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($title, fn($query) => $query->where("title", "like", "%$title%"))
            ->when($status, fn($query) => $query->where("status", $status));

            if ($isPaginate) {
                $banners = $banners->orderBy('created_at', 'desc')->paginate($paginateSize);
            } else {
                $banners = $banners->orderBy('title', 'asc')->get();
            }

            return $banners;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $banner = $this->model->onlyTrashed()->find($id);
            if (!$banner) {
                throw new CustomException("Banner not found");
            }

            $banner->restore();

            return $banner;

        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $banner = $this->model->withTrashed()->find($id);
            if (!$banner) {
                throw new CustomException("banner not found");
            }

            return $banner->forceDelete();

        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
