<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class PermissionRepository
{
    public function __construct(protected Permission $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $displayName  = $request->input('display_name', null);

        try {
            $permissions = $this->model->with(["createdBy:id,username"])
            ->when($displayName, fn ($query) => $query->where('display_name', 'like', "%$displayName%"))
            ->orderBy('display_name', 'asc')
            ->paginate($paginateSize);

            return $permissions;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $permission = new $this->model();

            $permission->display_name = $request->display_name;
            $permission->name         = Str::slug($request->display_name);
            $permission->group        = $request->group;
            $permission->description  = $request->description;
            $permission->save();

            DB::commit();

            return $permission;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $permission = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$permission) {
                throw new CustomException('Permission not found');
            }

            return $permission;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $permission = $this->model->find($id);

            if (!$permission) {
                throw new CustomException('Permission not found');
            }

            $permission->display_name = $request->display_name;
            $permission->name         = Str::slug($request->display_name);
            $permission->group        = $request->group;
            $permission->description  = $request->description;
            $permission->save();

            DB::commit();

            return $permission;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $permission = $this->model->find($id);
            if (!$permission) {
                throw new CustomException('Permission not found');
            }

            return $permission->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $displayName  = $request->input('display_name', null);

        try {
            $permissions = $this->model->with(["createdBy"])
            ->onlyTrashed()
            ->when($displayName, fn ($query) => $query->where('display_name', 'like', "%$displayName%"))
            ->orderBy('display_name', 'asc')
            ->paginate($paginateSize);

            return $permissions;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $permission = $this->model->onlyTrashed()->find($id);
            if (!$permission) {
                throw new CustomException('Permission not found');
            }

            $permission->restore();

            return $permission;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $permission = $this->model->withTrashed()->find($id);
            if (!$permission) {
                throw new CustomException('Permission not found');
            }

            return $permission->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
