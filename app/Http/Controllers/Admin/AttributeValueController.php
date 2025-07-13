<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\AttributeValueRepository;
use App\Http\Resources\Admin\AttributeValueResource;
use App\Http\Resources\Admin\AttributeValueCollection;
use App\Http\Requests\Admin\StoreAttributeValueRequest;
use App\Http\Requests\Admin\UpdateAttributeValueRequest;

class AttributeValueController extends BaseController
{
    public function __construct(protected AttributeValueRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('attribute-values-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $attributeValues = $this->repository->index($request);

            $attributeValues = new AttributeValueCollection($attributeValues);

            return $this->sendResponse($attributeValues, 'Attribute value list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(StoreAttributeValueRequest $request)
    {
        if (!$request->user()->hasPermission('attribute-values-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $attributeValue = $this->repository->store($request);

            $attributeValue = new AttributeValueResource($attributeValue);

            return $this->sendResponse($attributeValue, 'Attribute value created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('attribute-values-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $attributeValue = $this->repository->show($id);

            $attributeValue = new AttributeValueResource($attributeValue);

            return $this->sendResponse($attributeValue, "Attribute value single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);

        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(UpdateAttributeValueRequest $request, $id)
    {
        if (!$request->user()->hasPermission('attributes-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $attributeValue = $this->repository->update($request, $id);

            $attributeValue = new AttributeValueResource($attributeValue);

            return $this->sendResponse($attributeValue, 'Attribute value updated successfully', 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('attribute-values-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $attributeValue = $this->repository->delete($id);

            return $this->sendResponse($attributeValue, 'Attribute value deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('attribute-values-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $attributeValues = $this->repository->trashList($request);

            $attributeValues = new AttributeValueCollection($attributeValues);

            return $this->sendResponse($attributeValues, 'Attribute value trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('attribute-values-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $attributeValue = $this->repository->restore($id);

            $attributeValue = new AttributeValueResource($attributeValue);

            return $this->sendResponse($attributeValue, "Attribute value restore successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('attribute-values-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $attributeValue = $this->repository->permanentDelete($id);

            return $this->sendResponse($attributeValue, "Attribute value permanently delete successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
