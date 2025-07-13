<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Models\OrderFrom;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class OrderFromRepository
{
    public function __construct(protected OrderFrom $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $orderFroms = $this->model->with(["createdBy:id,username"])
            ->when($searchKey, fn($query) => $query->where("name", "like", "%$searchKey%"))
            ->orderBy('created_at', 'desc')->paginate($paginateSize);

            return $orderFroms;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $orderFrom = new $this->model();

            $orderFrom->name   = $request->name;
            $orderFrom->slug   = $request->name;
            $orderFrom->status = $request->status;
            $orderFrom->save();

            DB::commit();

            return $orderFrom;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $orderFrom = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$orderFrom) {
                throw new CustomException("Order From not found");
            }

            return $orderFrom;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $orderFrom = $this->model->find($id);
            if (!$orderFrom) {
                throw new CustomException("Order From Not found");
            }

            $orderFrom->name   = $request->name;
            $orderFrom->slug   = $request->name;
            $orderFrom->status = $request->status;
            $orderFrom->save();

            DB::commit();

            return $orderFrom;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $orderFrom = $this->model->find($id);
            if (!$orderFrom) {
                throw new CustomException('Order Froms not found');
            }

            return $orderFrom->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $orderFroms = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($searchKey, fn($query) => $query->where("name", "like", "%$searchKey%"))
            ->orderBy('created_at', 'desc')->paginate($paginateSize);

            return $orderFroms;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $orderFrom = $this->model->onlyTrashed()->find($id);
            if (!$orderFrom) {
                throw new CustomException('Order Froms not found');
            }

            $orderFrom->restore();

            return $orderFrom;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $orderFrom = $this->model->withTrashed()->find($id);
            if (!$orderFrom) {
                throw new CustomException('Order From not found');
            }

            return $orderFrom->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
