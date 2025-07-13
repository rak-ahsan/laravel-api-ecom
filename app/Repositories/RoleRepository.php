<?php

namespace App\Repositories;

use Exception;
use App\Models\Role;
use App\Classes\Helper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class RoleRepository
{
    public function __construct(protected Role $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $roles = $this->model->with(["createdBy:id,username"])
            ->when($searchKey, fn ($query) => $query->where('display_name', 'like', "%$searchKey%"))
            ->orderBy('display_name', 'asc')
            ->paginate($paginateSize);

            return $roles;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $role = new $this->model();

            $role->display_name = $request->display_name;
            $role->name         = Str::slug($request->display_name, '-');
            $role->description  = $request->description;
            $role->save();

            if (count($request->permission_ids) > 0) {
                $role->syncPermissions($request->permission_ids);
            }

            DB::commit();

            return $role;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {

            $role = $this->model->with('permissions')->find($id);

            if (!$role) {
                throw new CustomException('Role not found');
            }

            return $role;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $role = $this->model->find($id);
            if (!$role) {
                throw new CustomException('Role not found');
            }

            $role->display_name = $request->display_name;
            $role->name         = Str::slug($request->display_name);
            $role->description  = $request->description;
            $role->save();

            if (count($request->permission_ids) > 0) {
                $role->syncPermissions($request->permission_ids);
            }

            DB::commit();

            return $role;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $role = $this->model->find($id);
            if (!$role) {
                throw new CustomException('Role not found');
            }

            return $role->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $roles = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($searchKey, fn ($query) => $query->where('display_name', 'like', "%$searchKey%"))
            ->orderBy('display_name', 'asc')
            ->paginate($paginateSize);

            return $roles;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $role = $this->model->onlyTrashed()->find($id);
            if (!$role) {
                throw new CustomException('Role not found');
            }

            $role->restore();

            return $role;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $role = $this->model->withTrashed()->find($id);
            if (!$role) {
                throw new CustomException('Role not found');
            }

            return $role->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
