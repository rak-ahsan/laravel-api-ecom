<?php

namespace App\Repositories;

use Exception;
use App\Models\About;
use App\Classes\Helper;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class AboutRepository
{
    public function __construct(protected About $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $title        = $request->input("title", null);

        try {
            $abouts = $this->model->with(["createdBy:id,username"])
            ->when($title, fn ($query) => $query->where("title", "like", "%$title%"))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $abouts;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $about = new $this->model();

            $about->title       = $request->title;
            $about->description = $request->description;
            $about->save();

            // Upload image
            if ($request->hasFile('image')) {
                Helper::uploadFile($about, $request->image, $about->uploadPath);
            }

            DB::commit();

            return $about;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $about = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$about) {
                throw new CustomException("About not found");
            }

            return $about;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $about = $this->model->find($id);
            if (!$about) {
                throw new CustomException("About not found");
            }

            $about->title       = $request->title;
            $about->description = $request->description;
            $about->save();

            // Upload image one
            if ($request->hasFile('image')) {
                Helper::uploadFile($about, $request->image, $about->uploadPath, $about->img_path);
            }

            DB::commit();

            return $about;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $about = $this->model->find($id);
            if (!$about) {
                throw new CustomException("About not found");
            }

            //  Delete old image one
            if ($about->img_path) {
                Helper::deleteFile($about->img_path);
            }

            return $about->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $title        = $request->input("title", null);

        try {
            $abouts = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($title, fn($query) => $query->where("title", "like", "%$title%"))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $abouts;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $about = $this->model->onlyTrashed()->find($id);
            if (!$about) {
                throw new CustomException("About not found");
            }

            $about->restore();

            return $about;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $about = $this->model->onlyTrashed()->find($id);
            if (!$about) {
                throw new CustomException("About not found");
            }

            return $about->forceDelete();

        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
