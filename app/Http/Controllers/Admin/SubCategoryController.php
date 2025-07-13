<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\SubCategoryRepository;
use App\Http\Requests\Admin\SubCategoryRequest;
use App\Http\Resources\Admin\SubCategoryResource;
use App\Http\Resources\Admin\SubCategoryCollection;

class SubCategoryController extends BaseController
{
    public function __construct(protected SubCategoryRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('sub-categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $subCategories = $this->repository->index($request);

            $subCategories = new SubCategoryCollection($subCategories);

            return $this->sendResponse($subCategories, 'Subcategory list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('sub-categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $subCategory = $this->repository->show($id);

            $subCategory = new SubCategoryResource($subCategory);

            return $this->sendResponse($subCategory, 'Subcategory single view', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(SubCategoryRequest $request)
    {
        if (!$request->user()->hasPermission('sub-categories-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $sub_category = $this->repository->store($request);

            $sub_category = new SubCategoryResource($sub_category);

            return $this->sendResponse($sub_category, 'Subcategory created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function update(SubCategoryRequest $request, $id)
    {
        if (!$request->user()->hasPermission('sub-categories-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $sub_category = $this->repository->update($request, $id);

            $sub_category = new SubCategoryResource($sub_category);

            return $this->sendResponse($sub_category, 'Subcategory updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('sub-categories-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $subCategory = $this->repository->delete($id);

            return $this->sendResponse($subCategory, 'Subcategory deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function getSubCategoryIdByCategoryId($id)
    {
        $sub_category = SubCategory::select('name', 'slug', 'status', 'id')->where('category_id', $id)->get();

        return $this->sendResponse($sub_category, 'Subcategory list successfully', 200);
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('sub-categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $subCategories = $this->repository->trashList($request);

            $subCategories = new SubCategoryCollection($subCategories);

            return $this->sendResponse($subCategories, 'Subcategory trust list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }


    function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('sub-categories-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $sub_category = $this->repository->restore($id);

            $sub_category = new SubCategoryResource($sub_category);

            return $this->sendResponse($sub_category, 'Subcategory restore successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }


    function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('sub-categories-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $sub_category = $this->repository->permanentDelete($id);

            return $this->sendResponse($sub_category, 'Subcategory permanently delete successfully', 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
