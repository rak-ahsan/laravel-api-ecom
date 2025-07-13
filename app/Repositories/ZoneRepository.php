<?php

namespace App\Repositories;

use Exception;
use App\Models\Zone;
use App\Classes\Helper;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class ZoneRepository
{
    public function __construct(protected Zone $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $isPaginate   = $request->input('is_paginate', true);
        $name         = $request->input('name', null);
        $status       = $request->input('status', null);

        try {
            $zones = $this->model->with(["createdBy:id,username"])
            ->when($name, fn ($query) => $query->where("name", "like", "%$name%"))
            ->when($status, fn ($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc');

            $zones = $isPaginate ? $zones->paginate($paginateSize) : $zones->get();

            return $zones;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $zone = new $this->model();

            $zone->name   = $request->name;
            $zone->slug   = $request->name;
            $zone->status = $request->status;
            $zone->save();

            DB::commit();

            return $zone;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $zone = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$zone) {
                throw new CustomException("Zone not found");
            }

            return $zone;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $zone = $this->model->find($id);

            $zone->name   = $request->name;
            $zone->slug   = $request->name;
            $zone->status = $request->status;
            $zone->save();

            DB::commit();

            return $zone;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $zone = $this->model->find($id);
            if (!$zone) {
                throw new CustomException('Zone not found');
            }

            return $zone->delete();
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
            $zones = $this->model->with(["createdBy:id,username"])
                ->onlyTrashed()
                ->when($name, fn($query) => $query->where("name", "like", "%$name%"))
                ->when($status, fn($query) => $query->where("status", $status))
                ->orderBy('created_at', 'desc')
                ->paginate($paginateSize);

            return $zones;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $zone = $this->model->onlyTrashed()->find($id);
            if (!$zone) {
                throw new CustomException('Zone not found');
            }

            $zone->restore();

            return $zone;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $zone = $this->model->withTrashed()->find($id);
            if (!$zone) {
                throw new CustomException('Zone not found');
            }

            return $zone->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
