<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Models\AttributeValue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class AttributeValueRepository
{
    public function __construct(protected AttributeValue $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $isPaginate   = $request->input("is_paginate", null);
        $attributeId  = $request->input("attribute_id", null);
        $value        = $request->input("value", null);

        try {
            $attributeValues = $this->model->with(["attribute:id,name", "createdBy:id,username"])
            ->when($value, fn ($query) => $query->where("value", "like", "%$value%"))
            ->when($attributeId, fn ($query) => $query->where("attribute_id", $attributeId))
            ->orderBy('created_at', 'desc');

            return $isPaginate ? $attributeValues->paginate($paginateSize) : $attributeValues->get();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $attributeValue = $this->model->firstOrCreate(
                [
                    'attribute_id' => $request->attribute_id,
                    "value"        => $request->value
                ],
            );

            $attributeValue->load('attribute:id,name');

            DB::commit();

            return $attributeValue;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $attributeValue = $this->model->with(["attribute:id,name", "createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$attributeValue) {
                throw new CustomException("Attribute value not found");
            }

            return $attributeValue;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $attributeValue = $this->model->find($id);
            if (!$attributeValue) {
                throw new CustomException("Attribute value not found");
            }

            $attributeValue->value = $request->value;
            $attributeValue->save();

            $attributeValue->load('attribute:id,name');

            DB::commit();

            return $attributeValue;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $attributeValue = $this->model->find($id);
            if (!$attributeValue) {
                throw new CustomException("Attribute value not found");
            }

            return $attributeValue->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $attributeId  = $request->input("attribute_id", null);
        $value        = $request->input("value", null);

        try {
            $attributeValues = $this->model->with(["attribute:id,name", "createdBy:id,username"])
            ->onlyTrashed()
            ->when($value, fn ($query) => $query->where("value", "like", "%$value%"))
            ->when($attributeId, fn ($query) => $query->where("attribute_id", $attributeId))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $attributeValues;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $attributeValue = $this->model->onlyTrashed()->find($id);
            if (!$attributeValue) {
                throw new CustomException("Attribute value not found");
            }

            $attributeValue->restore();

            return $attributeValue;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $attributeValue = $this->model->withTrashed()->find($id);
            if (!$attributeValue) {
                throw new CustomException("Attribute value not found");
            }

            return $attributeValue->forceDelete();

        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
