<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Enums\StatusEnum;
use Illuminate\Support\Str;
use App\Models\DeliveryGateway;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class DeliveryGatewayRepository
{
    public function __construct(protected DeliveryGateway $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $deliveryGateways = $this->model->with(["createdBy:id,username"])
            ->when($searchKey, fn($query) => $query->where("name", "like", "%$searchKey%"))
            ->orderBy('created_at', 'desc')->paginate($paginateSize);

            return $deliveryGateways;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $deliveryGateway = new $this->model();

            $deliveryGateway->name         = $request->name;
            $deliveryGateway->min_time     = $request->minTime;
            $deliveryGateway->max_time     = $request->maxTime;
            $deliveryGateway->time_unit    = $request->timeUnit;
            $deliveryGateway->delivery_fee = $request->deliveryFee;
            $deliveryGateway->slug         = Str::slug($request->name);
            $deliveryGateway->status       = $request->status ?? StatusEnum::ACTIVE->value;
            $deliveryGateway->save();

            DB::commit();

            return $deliveryGateway;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $deliveryGateway = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$deliveryGateway) {
                throw new CustomException("DeliveryGateway not found");
            }

            return $deliveryGateway;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $deliveryGateway = $this->model->find($id);
            if (!$deliveryGateway) {
                throw new CustomException("Delivery Gateway Not found");
            }

            $deliveryGateway->name         = $request->name;
            $deliveryGateway->min_time     = $request->min_time;
            $deliveryGateway->max_time     = $request->max_time;
            $deliveryGateway->time_unit    = $request->time_unit;
            $deliveryGateway->delivery_fee = $request->delivery_fee;
            $deliveryGateway->slug         = Str::slug($request->name);
            $deliveryGateway->status       = $request->status ?? StatusEnum::ACTIVE->value;
            $deliveryGateway->save();

            DB::commit();

            return $deliveryGateway;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $deliveryGateway = $this->model->find($id);
            if (!$deliveryGateway) {
                throw new CustomException('DeliveryGateway not found');
            }

            return $deliveryGateway->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);
        $status       = $request->input("status", null);

        try {
            $deliveryGateways = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($searchKey, fn($query) => $query->where("name", "like", "%$searchKey%"))
            ->when($status, fn($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc')->paginate($paginateSize);

            return $deliveryGateways;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $deliveryGateway = $this->model->onlyTrashed()->find($id);
            if (!$deliveryGateway) {
                throw new CustomException('DeliveryGateway not found');
            }

            $deliveryGateway->restore();

            return $deliveryGateway;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $deliveryGateway = $this->model->withTrashed()->find($id);
            if (!$deliveryGateway) {
                throw new CustomException('DeliveryGateway not found');
            }

            return $deliveryGateway->forceDelete();

        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
