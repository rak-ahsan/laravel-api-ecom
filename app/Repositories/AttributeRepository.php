<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Models\Attribute;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class AttributeRepository
{
    public function __construct(protected Attribute $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $attributes = $this->model->with(["createdBy:id,username", "attributeValues"])
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("name", "like", "%$searchKey%")
                    ->orWhere("status", $searchKey);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $attributes;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $attribute = new $this->model();

            $attribute->name   = $request->name;
            $attribute->slug   = $request->name;
            $attribute->status = $request->status;
            $attribute->save();

            DB::commit();

            return $attribute;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $attribute = $this->model->with(["createdBy:id,username", "updatedBy:id,username", "attributeValues"])->find($id);

            if (!$attribute) {
                throw new CustomException("Attribute not found");
            }

            return $attribute;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $attribute = $this->model->find($id);
            if (!$attribute) {
                throw new CustomException("Attribute not found");
            }

            $attribute->name   = $request->name;
            $attribute->slug   = $request->name;
            $attribute->status = $request->status;
            $attribute->save();

            DB::commit();

            return $attribute;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $attribute = $this->model->find($id);
            if (!$attribute) {
                throw new CustomException("Attribute not found");
            }

            return $attribute->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $attributes = $this->model->with(["createdBy:id,username"])
                ->onlyTrashed()
                ->when($searchKey, function ($query) use ($searchKey) {
                    $query->where("name", "like", "%$searchKey%")
                        ->orWhere("status", $searchKey);
                })
                ->orderBy('created_at', 'desc')
                ->paginate($paginateSize);

            return $attributes;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $attribute = $this->model->onlyTrashed()->find($id);
            if (!$attribute) {
                throw new CustomException("Attributes not found");
            }
            $attribute->restore();

            return $attribute;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $attribute = $this->model->withTrashed()->find($id);
            if (!$attribute) {
                throw new CustomException("Attribute not found");
            }

            return $attribute->forceDelete();

        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
