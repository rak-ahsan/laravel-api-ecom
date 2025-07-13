<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class PaymentGatewayRepository
{
    public function __construct(protected PaymentGateway $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $paymentGateways = $this->model->with(["createdBy:id,username"])
            ->when($searchKey, fn($query) => $query->where("name", "like", "%$searchKey%"))
            ->orderBy('created_at', 'desc')->paginate($paginateSize);

            return $paymentGateways;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $paymentGateway = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$paymentGateway) {
                throw new CustomException('PaymentGateway not found');
            }

            return $paymentGateway;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $paymentGateway = new $this->model();

            $paymentGateway->name   = $request->name;
            $paymentGateway->slug   = $request->name;
            $paymentGateway->status = $request->status;
            $paymentGateway->save();

            //Upload image
            if ($request->hasFile('image')) {
                Helper::uploadFile($paymentGateway, $request->image, $paymentGateway->uploadPath);
            }

            DB::commit();

            return $paymentGateway;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $paymentGateway = $this->model->find($id);
            if (!$paymentGateway) {
                throw new CustomException("Payment Gateway Not found");
            }

            $paymentGateway->name   = $request->name;
            $paymentGateway->slug   = $request->name;
            $paymentGateway->status = $request->status;
            $paymentGateway->save();

            // Update image
            if ($request->hasFile('image')) {
                Helper::uploadFile($paymentGateway, $request->image, $paymentGateway->uploadPath, $paymentGateway->img_path);
            }

            DB::commit();

            return $paymentGateway;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $paymentGateway = $this->model->find($id);
            if (!$paymentGateway) {
                throw new CustomException("Payment Gateway not found");
            }
            //  Delete old image
            if ($paymentGateway->img_path) {
                Helper::deleteFile($paymentGateway->img_path);
            }

            return $paymentGateway->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $paymentGateways = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($searchKey, fn($query) => $query->where("name", "like", "%$searchKey%"))
            ->paginate($paginateSize);

            return $paymentGateways;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $paymentGateway = $this->model->onlyTrashed()->find($id);
            if (!$paymentGateway) {
                throw new CustomException("Payment Gateway not found");
            }

            $paymentGateway->restore();

            return $paymentGateway;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $paymentGateway = $this->model->withTrashed()->find($id);
            if (!$paymentGateway) {
                throw new CustomException("Payment Gateway not found");
            }

            return $paymentGateway->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
