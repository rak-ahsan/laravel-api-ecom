<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class SupplierRepository
{
    public function __construct(protected Supplier $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $isPaginate   = $request->input('is_paginate', true);
        $name         = $request->input('name', null);
        $status       = $request->input('status', null);

        try {
            $suppliers = $this->model->with(["createdBy:id,username"])
            ->when($name, fn($query) => $query->where("name", "like", "%$name%"))
            ->when($status, fn($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc');

            if ($isPaginate) {
                $suppliers = $suppliers->paginate($paginateSize);
            } else {
                $suppliers = $suppliers->get();
            }

            return $suppliers;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $supplier = new $this->model();

            $supplier->name         = $request->name;
            $supplier->phone_number = $request->phone_number;
            $supplier->email        = $request->email;
            $supplier->address      = $request->address;
            $supplier->status       = $request->status;
            $supplier->save();

            DB::commit();

            return $supplier;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $supplier = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$supplier) {
                throw new CustomException("Supplier not found");
            }

            return $supplier;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $supplier = $this->model->find($id);
            if (!$supplier) {
                throw new CustomException("Supplier not found");
            }

            $supplier->name         = $request->name;
            $supplier->phone_number = $request->phone_number;
            $supplier->email        = $request->email;
            $supplier->address      = $request->address;
            $supplier->status       = $request->status;
            $supplier->save();

            DB::commit();

            return $supplier;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $supplier = $this->model->find($id);
            if (!$supplier) {
                throw new CustomException("Supplier not found");
            }

            return $supplier->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $isPaginate   = $request->input('is_paginate', true);
        $name         = $request->input('name', null);
        $status       = $request->input('status', null);

        try {
            $suppliers = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($name, fn($query) => $query->where("name", "like", "%$name%"))
            ->when($status, fn($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc');

            if ($isPaginate) {
                $suppliers = $suppliers->paginate($paginateSize);
            } else {
                $suppliers = $suppliers->get();
            }

            return $suppliers;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $supplier = $this->model->onlyTrashed()->find($id);
            if (!$supplier) {
                throw new CustomException("Supplier not found");
            }

            $supplier->restore();

            return $supplier;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $supplier = $this->model->withTrashed()->find($id);
            if (!$supplier) {
                throw new CustomException("Supplier not found");
            }

            return $supplier->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
