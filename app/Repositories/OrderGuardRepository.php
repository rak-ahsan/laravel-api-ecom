<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Enums\StatusEnum;
use App\Models\OrderGuard;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class OrderGuardRepository
{
    public function __construct(protected OrderGuard $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $status       = $request->input("status", null);

        try {
            $guards = $this->model->with(["createdBy:id,username"])
            ->when($status, fn($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $guards;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $guard = new $this->model();

            $guard->quantity      = $request->quantity;
            $guard->duration      = $request->duration;
            $guard->duration_type = $request->duration_type;
            $guard->status        = $request->status ?? StatusEnum::ACTIVE->value;
            $guard->save();

            DB::commit();

            return $guard;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id, $status = null)
    {
        try {
            $guard = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])
            ->when($status, fn($query) => $query->where("status", $status))
            ->find($id);

            if (!$guard) {
                throw new CustomException("Order guard not found");
            }
            return $guard;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $guard = $this->model->find($id);
            if (!$guard) {
                throw new CustomException("Order guard Not found");
            }

            $guard->quantity      = $request->quantity;
            $guard->duration      = $request->duration;
            $guard->duration_type = $request->duration_type;
            $guard->status        = $request->status ?? StatusEnum::ACTIVE->value;
            $guard->save();

            DB::commit();

            return $guard;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $guard = $this->model->find($id);
            if (!$guard) {
                throw new CustomException("Order guard not found");
            }

            return $guard->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
