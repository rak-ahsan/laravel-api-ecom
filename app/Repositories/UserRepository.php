<?php

namespace App\Repositories;

use Exception;
use App\Models\User;
use App\Classes\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\CustomException;

class UserRepository
{
    public function __construct(protected User $model){}

    public function index($request)
    {
        $paginateSize   = Helper::checkPaginateSize($request);
        $searchKey      = $request->input('search_key', null);
        $userCategoryId = $request->input('user_category_id', null);

        try {
            $users = $this->model->with([
                "userCategory:id,name",
                "roles:id,name,display_name",
                "roles.permissions:id,name,display_name"
            ])
            ->when($userCategoryId, fn ($query) => $query->where("user_category_id", $userCategoryId))
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("username", "like", "%$searchKey%")
                    ->orWhere("phone_number", "like", "%$searchKey%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $users;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $user = new $this->model();

            $user->username         = $request->username;
            $user->email            = $request->email;
            $user->phone_number     = $request->phone_number;
            $user->status           = $request->status;
            $user->user_category_id = $request->user_category_id;
            $user->password         = Hash::make($request->password);
            $user->save();

            $user->syncRoles($request->role_ids ?? []);

            if ($user) {
                // Upload image
                Helper::uploadFile($user, $request->image, $user->uploadPath);
            }

            DB::commit();

            return $user;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $user = $this->model->with([
                "userCategory:id,name",
                "roles:id,name,display_name",
                "roles.permissions:id,name,display_name"
            ])->find($id);

            if (!$user) {
                throw new CustomException("User not found");
            }

            return $user;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $user = $this->model->find($id);
            if (!$user) {
                throw new CustomException('User not found');
            }

            $user->username     = $request->username;
            $user->email        = $request->email;
            $user->phone_number = $request->phone_number;
            $user->status       = $request->status;
            $user->user_category_id = $request->user_category_id;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            $user->syncRoles($request->role_ids ?? []);

            if ($request->hasFile('image')) {
                Helper::uploadFile($user, $request->image, $user->uploadPath, $user->img_path);
            }

            DB::commit();

            return $user;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $user = $this->model->find($id);
            if (!$user) {
                throw new CustomException('User not found');
            }

            //  Delete old image
            if ($user->img_path) {
                Helper::deleteFile($user->img_path);
            }

            return $user->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey = $request->input('search_key', null);

        try {
            $users = $this->model->with('roles')
            ->onlyTrashed()
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("username", "like", "%$searchKey%")
                    ->orWhere("phone_number", "like", "%$searchKey%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $users;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $user = $this->model->onlyTrashed()->find($id);
            if (!$user) {
                throw new CustomException('User not found');
            }

            $user->restore();

            return $user;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $user = $this->model->withTrashed()->find($id);
            if (!$user) {
                throw new CustomException('User not found');
            }

            return $user->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}




