<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Models\TermsAndCondition;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class TermsAndConditionRepository
{
    public function __construct(protected TermsAndCondition $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $title        = $request->input('title', null);
        $status       = $request->input('status', null);

        try {
            $terms = $this->model->with(["createdBy:id,username"])
            ->when($title, fn ($query) => $query->where("title", "like", "%$title%"))
            ->when($status, fn ($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $terms;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $term = new $this->model();

            $term->title       = $request->title;
            $term->description = $request->description;
            $term->status      = $request->status;
            $term->save();

            DB::commit();

            return $term;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $term = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$term) {
                throw new CustomException("Terms And Condition not found");
            }

            return $term;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $term = $this->model->findOrFail($id);

            $term->title       = $request->title;
            $term->description = $request->description;
            $term->status      = $request->status;
            $term->save();

            DB::commit();

            return $term;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $term = $this->model->find($id);
            if (!$term) {
                throw new CustomException('Terms And Condition not found');
            }

            return $term->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $isPaginate   = $request->input('is_paginate', true);
        $title        = $request->input('title', null);
        $status       = $request->input('status', null);

        try {
            $terms = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($title, fn($query) => $query->where("title", "like", "%$title%"))
            ->when($status, fn($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $terms;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $term = $this->model->onlyTrashed()->find($id);
            if (!$term) {
                throw new CustomException('Terms And Condition not found');
            }

            $term->restore();

            return $term;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $term = $this->model->withTrashed()->find($id);
            if (!$term) {
                throw new CustomException('Terms And Condition not found');
            }

            return $term->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
