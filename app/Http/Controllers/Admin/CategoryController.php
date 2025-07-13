<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\CategoryRepository;
use App\Http\Requests\Admin\CategoryRequest;
use App\Http\Resources\Admin\CategoryResource;
use App\Http\Resources\Admin\CategoryCollection;

class CategoryController extends BaseController
{
    public function __construct(protected CategoryRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $categories = $this->repository->index($request);

            $categories = new CategoryCollection($categories);

            return $this->sendResponse($categories, 'Category list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(CategoryRequest $request)
    {
        if (!$request->user()->hasPermission('categories-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $category = $this->repository->store($request);

            $category = new CategoryResource($category);

            return $this->sendResponse($category, 'Category created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $category = $this->repository->show($id);

            $category = new CategoryResource($category);

            return $this->sendResponse($category, "Category single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function update(CategoryRequest $request, $id)
    {
        if (!$request->user()->hasPermission('categories-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $category = $this->repository->update($request, $id);

            $category = new CategoryResource($category);

            return $this->sendResponse($category, 'Category updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('categories-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $category = $this->repository->delete($id);

            return $this->sendResponse($category, 'Category deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $categories = $this->repository->trashList($request);

            $categories = new CategoryCollection($categories);

            return $this->sendResponse($categories, 'Category trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('categories-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $category = $this->repository->restore($id);

            $category = new CategoryResource($category);

            return $this->sendResponse($category, "Category restore successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (CustomException $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('categories-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $category = $this->repository->permanentDelete($id);

            return $this->sendResponse($category, "Category permanently delete successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
