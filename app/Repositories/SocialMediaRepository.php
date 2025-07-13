<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Models\SocialMedia;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class SocialMediaRepository
{
    public function __construct(protected SocialMedia $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $title        = $request->input("title", null);
        $status       = $request->input("status", null);

        try {
            $socialMedias = $this->model->with(["createdBy:id,username"])
            ->when($title, fn ($query) => $query->where("title", "like", "%$title%"))
            ->when($status, fn ($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $socialMedias;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $socialMedia = new $this->model();

            $socialMedia->title  = $request->title;
            $socialMedia->link   = $request->link;
            $socialMedia->status = $request->status;
            $socialMedia->status = $request->status;
            $socialMedia->save();

            // Upload image
            if ($request->hasFile('image')) {
                Helper::uploadFile($socialMedia, $request->image, $socialMedia->uploadPath);
            }

            DB::commit();

            return $socialMedia;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $socialMedia = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$socialMedia) {
                throw new CustomException("Social media not found");
            }

            return $socialMedia;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $socialMedia = $this->model->find($id);
            if (!$socialMedia) {
                throw new CustomException("Social media not found");
            }

            $socialMedia->title  = $request->title;
            $socialMedia->link   = $request->link;
            $socialMedia->status = $request->status;
            $socialMedia->status = $request->status;
            $socialMedia->save();

            // Upload image one
            if ($request->hasFile('image')) {
                Helper::uploadFile($socialMedia, $request->image, $socialMedia->uploadPath, $socialMedia->img_path);
            }

            DB::commit();

            return $socialMedia;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $socialMedia = $this->model->find($id);
            if (!$socialMedia) {
                throw new CustomException("Social media not found");
            }

            //  Delete old image one
            if ($socialMedia->img_path) {
                Helper::deleteFile($socialMedia->img_path);
            }

            return $socialMedia->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $title        = $request->input("title", null);
        $status       = $request->input("status", null);

        try {
            $socialMedias = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($title, fn($query) => $query->where("title", "like", "%$title%"))
            ->when($status, fn($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $socialMedias;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $socialMedia = $this->model->onlyTrashed()->find($id);
            if (!$socialMedia) {
                throw new CustomException("Social media not found");
            }

            $socialMedia->restore();

            return $socialMedia;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $socialMedia = $this->model->withTrashed()->find($id);
            if (!$socialMedia) {
                throw new CustomException("Social media not found");
            }

            return $socialMedia->forceDelete();

        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
