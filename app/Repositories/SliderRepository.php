<?php

namespace App\Repositories;

use Exception;
use App\Models\Slider;
use App\Classes\Helper;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class SliderRepository
{
    public function __construct(protected Slider $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);
        $status       = $request->input("status", null);

        try {
            $sliders = $this->model->orderBy('created_at', 'desc')
            ->when($searchKey, fn ($query) => $query->where("title", "like", "%$searchKey%"))
            ->when($status, fn ($query) => $query->where("status", $status))
            ->paginate($paginateSize);

            return $sliders;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $slider = new $this->model();

            $slider->title  = $request->title;
            $slider->status = $request->status;
            $slider->save();

            // Upload image
            if ($request->hasFile('image')) {
                Helper::uploadFile($slider, $request->image, $slider->uploadPath);
            }


            DB::commit();

            return $slider;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $slider = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$slider) {
                throw new CustomException('Slider not found');
            }

            return $slider;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $slider = $this->model->find($id);

            if (!$slider) {
                throw new CustomException("Slider not found");
            }

            $slider->title  = $request->title;
            $slider->status = $request->status;
            $slider->save();

            if ($request->hasFile('image')) {
                // Update image
                Helper::uploadFile($slider, $request->image, $slider->uploadPath, $slider->img_path);
            }

            DB::commit();

            return $slider;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $slider = $this->model->find($id);
            if (!$slider) {
                throw new CustomException('Slider not found');
            }
            // Delete old image
            if ($slider->img_path) {
                Helper::deleteFile($slider->img_path);
            }

            return $slider->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);
        $status       = $request->input("status", null);

        try {
            $sliders = $this->model->orderBy('created_at', 'desc')
            ->onlyTrashed()
            ->when($searchKey, fn ($query) => $query->where("title", "like", "%$searchKey%"))
            ->when($status, fn ($query) => $query->where("status", $status))
            ->paginate($paginateSize);

            return $sliders;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $slider = $this->model->onlyTrashed()->find($id);
            if (!$slider) {
                throw new CustomException('Slider not found');
            }

            $slider->restore();

            return $slider;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $slider = $this->model->withTrashed()->find($id);
            if (!$slider) {
                throw new CustomException('Slider not found');
            }

            return $slider->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
