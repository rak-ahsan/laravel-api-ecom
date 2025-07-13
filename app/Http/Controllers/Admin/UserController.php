<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\UserRepository;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Http\Resources\Admin\UserCollection;

class UserController extends BaseController
{
    public function __construct(protected UserRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('users-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $users = $this->repository->index($request);

            $users = new UserCollection($users);

            return $this->sendResponse($users, 'User list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(UserRequest $request)
    {
        if (!$request->user()->hasPermission('users-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $user = $this->repository->store($request);

            $user = new UserResource($user);

            return $this->sendResponse($user, 'User updated successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('users-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $user = $this->repository->show($id);

            $user = new UserResource($user);

            return $this->sendResponse($user, 'User single view', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(UserRequest $request, $id)
    {
        if (!$request->user()->hasPermission('users-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $user = $this->repository->update($request, $id);

            $user = new UserResource($user);

            return $this->sendResponse($user, 'User updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function userPermission()  //Ceecking...
    {
        $id = auth()->id();

        try {
            $user = User::with("roles:id,name,display_name", "roles.permissions:id,name,display_name")->find($id);

            return $this->sendResponse($user, 'User permissions', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('users-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $user = $this->repository->delete($id);

            return $this->sendResponse(null, 'User deleted successfully', 200);
        }catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('users-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $users = $this->repository->trashList($request);

            $users = new UserCollection($users);

            return $this->sendResponse($users, 'User trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('users-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $user = $this->repository->restore($id);

            $user = new UserResource($user);

            return $this->sendResponse($user, 'User restore successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('users-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $user = $this->repository->permanentDelete($id);


            return $this->sendResponse($user, 'User permanently deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
