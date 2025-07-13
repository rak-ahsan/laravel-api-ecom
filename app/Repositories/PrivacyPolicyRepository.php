<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Enums\StatusEnum;
use App\Models\PrivacyPolicy;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class PrivacyPolicyRepository
{
    public function __construct(protected PrivacyPolicy $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $title        = $request->input('title', null);
        $status       = $request->input('status', null);

        try {
            $Privacies = $this->model->with(["createdBy:id,username"])
            ->when($title, fn ($query) => $query->where("title", "like", "%$title%"))
            ->when($status, fn ($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $Privacies;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $Privacy = new $this->model();

            $Privacy->title       = $request->title;
            $Privacy->description = $request->description;
            $Privacy->status      = $request->status ?? StatusEnum::ACTIVE->value;
            $Privacy->save();

            DB::commit();

            return $Privacy;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $Privacy = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$Privacy) {
                throw new CustomException("Privacy Policy not found");
            }

            return $Privacy;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $Privacy = $this->model->find($id);
            if (!$Privacy) {
                throw new CustomException("Privacy Policy not found");
            }

            $Privacy->title       = $request->title;
            $Privacy->description = $request->description;
            $Privacy->status      = $request->status;
            $Privacy->save();

            DB::commit();

            return $Privacy;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $Privacy = $this->model->find($id);
            if (!$Privacy) {
                throw new CustomException("Privacy Policy not found");
            }

            return $Privacy->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $title        = $request->input('title', null);
        $status       = $request->input('status', null);

        try {
            $Privacies = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($title, fn($query) => $query->where("title", "like", "%$title%"))
            ->when($status, fn($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $Privacies;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $Privacy = $this->model->onlyTrashed()->find($id);
            if (!$Privacy) {
                throw new CustomException("Privacy Policy not found");
            }

            $Privacy->restore();

            return $Privacy;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $Privacy = $this->model->withTrashed()->find($id);
            if (!$Privacy) {
                throw new CustomException("Privacy Policy not found");
            }

            return $Privacy->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
