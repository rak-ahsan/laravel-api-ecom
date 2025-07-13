<?php

namespace App\Repositories;

use Exception;
use App\Models\Tag;
use App\Classes\Helper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class TagRepository
{
    public function __construct(protected Tag $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $isPaginate   = $request->input("is_paginate");
        $searchKey    = $request->input("search_key", null);

        try {
            $tags = $this->model->with(["createdBy:id,username"])
            ->when($searchKey, function ($query) use ($searchKey){
                $query->where("name", "like", "%$searchKey%")
                    ->orWhere("status", $searchKey);
            })
            ->orderBy('created_at', 'desc');

            $tags = $isPaginate ? $tags->paginate($paginateSize) : $tags->get();

            return $tags;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $tag = new $this->model();

            $tag->name   = $request->name;
            $tag->slug   = Str::slug($request->name);
            $tag->status = $request->status;
            $tag->save();

            DB::commit();

            return $tag;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $tag = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$tag) {
                throw new CustomException("Tag not found");
            }

            return $tag;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $tag = $this->model->find($id);
            if (!$tag) {
                throw new CustomException("Tag Not found");
            }

            $tag->name    = $request->name;
            $tag->slug    = Str::slug($request->name);
            $tag->status  = $request->status;
            $tag->save();

            DB::commit();

            return $tag;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $tag = $this->model->find($id);
            if (!$tag) {
                throw new CustomException("Tag not found");
            }

            return $tag->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $paginateSize = $request->input('paginate_size', true);
        $searchKey    = $request->input("search_key", null);
        $status       = $request->input("status", null);

        try {
            $tags = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($searchKey, fn($query) => $query->where("name", "like", "%$searchKey%"))
            ->when($status, fn($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $tags;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $tag = $this->model->onlyTrashed()->find($id);
            if (!$tag) {
                throw new CustomException("Tag not found");
            }

            $tag->restore();

            return $tag;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $tag = $this->model->withTrashed()->find($id);
            if (!$tag) {
                throw new CustomException("Tag not found");
            }

            return $tag->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
