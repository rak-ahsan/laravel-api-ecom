<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\SettingCategoryRepository;
use App\Http\Requests\Admin\SettingCategoryRequest;
use App\Http\Resources\Admin\SettingCategoryResource;
use App\Http\Resources\Admin\SettingCategoryCollection;

class SettingCategoryController extends BaseController
{
    public function __construct(protected SettingCategoryRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('setting-category-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $settingCategories = $this->repository->index($request);

            $settingCategories = new SettingCategoryCollection($settingCategories);

            return $this->sendResponse($settingCategories, 'Setting category list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(SettingCategoryRequest $request)
    {
        if (!$request->user()->hasPermission('setting-category-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $settingCategory = $this->repository->store($request);

            $settingCategory = new SettingCategoryResource($settingCategory);

            return $this->sendResponse($settingCategory, 'Setting category created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('setting-category-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $settingCategory = $this->repository->show($id);

            $settingCategory = new settingCategoryResource($settingCategory);

            return $this->sendResponse($settingCategory, "Setting category single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(settingCategoryRequest $request, $id)
    {
        if (!$request->user()->hasPermission('setting-category-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $settingCategory = $this->repository->update($request, $id);

            $settingCategory = new settingCategoryResource($settingCategory);

            return $this->sendResponse($settingCategory, 'Setting category updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('setting-category-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $settingCategory = $this->repository->delete($id);

            return $this->sendResponse($settingCategory, 'Setting category deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('setting-category-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $settingCategories = $this->repository->trashList($request);

            $settingCategories = new SettingCategoryCollection($settingCategories);

            return $this->sendResponse($settingCategories, 'Setting category trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('setting-category-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $settingCategory = $this->repository->restore($id);

            $settingCategory = new SettingCategoryResource($settingCategory);

            return $this->sendResponse($settingCategory, 'Setting category restore successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('setting-category-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $settingCategory = $this->repository->permanentDelete($id);

            return $this->sendResponse($settingCategory, 'Setting category permanently deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
