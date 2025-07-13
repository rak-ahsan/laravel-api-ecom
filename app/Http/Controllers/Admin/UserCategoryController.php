<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\UserCategoryRepository;
use App\Http\Requests\Admin\UserCategoryRequest;
use App\Http\Resources\Admin\UserCategoryResource;
use App\Http\Resources\Admin\UserCategoryCollection;

class UserCategoryController extends BaseController
{
    public function __construct(protected UserCategoryRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('user-categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $userCategories = $this->repository->index($request);

            $userCategories = new UserCategoryCollection($userCategories);

            return $this->sendResponse($userCategories, 'User category list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(UserCategoryRequest $request)
    {
        if (!$request->user()->hasPermission('user-categories-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $userCategory = $this->repository->store($request);

            $userCategory = new UserCategoryResource($userCategory);

            return $this->sendResponse($userCategory, 'User category created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('user-categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $userCategory = $this->repository->show($id);

            $userCategory = new UserCategoryResource($userCategory);

            return $this->sendResponse($userCategory, "User category single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function update(UserCategoryRequest $request, $id)
    {
        if (!$request->user()->hasPermission('user-categories-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $userCategory = $this->repository->update($request, $id);

            $userCategory = new UserCategoryResource($userCategory);

            return $this->sendResponse($userCategory, 'User category updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('user-categories-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $userCategory = $this->repository->delete($id);

            return $this->sendResponse($userCategory, 'User category deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('user-categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $userCategory = $this->repository->trashList($request);

            $userCategory = new UserCategoryCollection($userCategory);

            return $this->sendResponse($userCategory, 'User Category trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('user-categories-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $userCategory = $this->repository->restore($id);

            $userCategory = new UserCategoryResource($userCategory);

            return $this->sendResponse($userCategory, "User category restore successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (CustomException $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('user-categories-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $userCategory = $this->repository->permanentDelete($id);

            return $this->sendResponse($userCategory, "User category permanently delete successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
