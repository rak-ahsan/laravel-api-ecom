<?php

namespace App\Repositories;

use Exception;
use App\Models\Team;
use App\Classes\Helper;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class TeamRepository
{
    public function __construct(protected Team $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);
        $status       = $request->input("status", null);

        try {
            $teams = $this->model->with(["createdBy:id,username"])->orderBy('created_at', 'desc')
            ->when($searchKey, fn($query) => $query->where("name", "like", "%$searchKey%"))
            ->when($status, fn ($query) => $query->where("status", $status))
            ->paginate($paginateSize);

            return $teams;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $team = new $this->model();

            $team->name        = $request->name;
            $team->designation = $request->designation;
            $team->status      = $request->status;
            $team->description = $request->description;
            $team->save();

            // Upload image
            if ($request->hasFile('image')) {
                Helper::uploadFile($team, $request->image, $team->uploadPath);
            }

            DB::commit();

            return $team;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $team = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$team) {
                throw new CustomException("Team not found");
            }

            return $team;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $team = $this->model->find($id);
            if (!$team) {
                throw new CustomException("Team not found");
            }

            $team->name        = $request->name;
            $team->designation = $request->designation;
            $team->status      = $request->status;
            $team->description = $request->description;
            $team->save();

            // Upload image
            if ($request->hasFile('image')) {
                Helper::uploadFile($team, $request->image, $team->uploadPath, $team->img_path);
            }

            DB::commit();

            return $team;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $team = $this->model->find($id);
            if (!$team) {
                throw new CustomException("Team not found");
            }

            //  Delete old image
            if ($team->img_path) {
                Helper::deleteFile($team->img_path);
            }

            return $team->delete();
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
            $teams = $this->model->with(["createdBy:id,username"])->orderBy('created_at', 'desc')
            ->onlyTrashed()
            ->when($searchKey, fn($query) => $query->where("name", "like", "%$searchKey%"))
            ->when($status, fn($query) => $query->where("status", $status))
            ->paginate($paginateSize);

            return $teams;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $team = $this->model->onlyTrashed()->find($id);
            if (!$team) {
                throw new CustomException("Team not found");
            }

            $team->restore();

            return $team;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $team = $this->model->withTrashed()->find($id);
            if (!$team) {
                throw new CustomException("Team not found");
            }

            return $team->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
