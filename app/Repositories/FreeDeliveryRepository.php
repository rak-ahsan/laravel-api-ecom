<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Models\FreeDelivery;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class FreeDeliveryRepository
{
    public function __construct(protected FreeDelivery $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $freeDeliveries = $this->model->with(["createdBy:id,username"])
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("name", "like", "%$searchKey%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $freeDeliveries;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $FreeDelivery = new $this->model();

            $FreeDelivery->type     = $request->type;
            $FreeDelivery->quantity = $request->quantity;
            $FreeDelivery->price    = $request->price;
            $FreeDelivery->status   = $request->status;
            $FreeDelivery->save();

            DB::commit();

            return $FreeDelivery;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $FreeDelivery = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$FreeDelivery) {
                throw new CustomException("Free delivery not found");
            }

            return $FreeDelivery;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $FreeDelivery = $this->model->find($id);
            if (!$FreeDelivery) {
                throw new CustomException("Free delivery not found");
            }

            $FreeDelivery->type     = $request->type;
            $FreeDelivery->quantity = $request->quantity;
            $FreeDelivery->price    = $request->price;
            $FreeDelivery->status   = $request->status;
            $FreeDelivery->save();

            DB::commit();

            return $FreeDelivery;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $FreeDelivery = $this->model->find($id);
            if (!$FreeDelivery) {
                throw new CustomException("Free delivery not found");
            }

            return $FreeDelivery->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $freeDeliveries = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("name", "like", "%$searchKey%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $freeDeliveries;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $FreeDelivery = $this->model->onlyTrashed()->find($id);
            if (!$FreeDelivery) {
                throw new CustomException("Free delivery not found");
            }
            $FreeDelivery->restore();

            return $FreeDelivery;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $FreeDelivery = $this->model->withTrashed()->find($id);
            if (!$FreeDelivery) {
                throw new CustomException("FreeDelivery not found");
            }

            return $FreeDelivery->forceDelete();

        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
