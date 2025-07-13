<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\AttributeRepository;
use App\Http\Requests\Admin\AttributeRequest;
use App\Http\Resources\Admin\AttributeResource;
use App\Http\Resources\Admin\AttributeCollection;
class AttributeController extends BaseController
{
    public function __construct(protected AttributeRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('attributes-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $attributes = $this->repository->index($request);

            $attributes = new AttributeCollection($attributes);

            return $this->sendResponse($attributes, 'Attribute list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(AttributeRequest $request)
    {
        if (!$request->user()->hasPermission('attributes-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $attribute = $this->repository->store($request);

            $attribute = new AttributeResource($attribute);

            return $this->sendResponse($attribute, 'Attribute created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('attributes-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $attribute = $this->repository->show($id);

            $attribute = new AttributeResource($attribute);

            return $this->sendResponse($attribute, 'Attribute single view', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(AttributeRequest $request, $id)
    {
        if (!$request->user()->hasPermission('attributes-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $attribute = $this->repository->update($request, $id);

            $attribute = new AttributeResource($attribute);

            return $this->sendResponse($attribute, 'Attribute updated successfully', 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('attributes-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $attribute = $this->repository->delete($id);

            return $this->sendResponse($attribute, 'Attribute deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('attributes-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $attributes = $this->repository->trashList($request);

            $attributes = new AttributeCollection($attributes);

            return $this->sendResponse($attributes, 'Attribute trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('attributes-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $attribute = $this->repository->restore($id);

            $attribute = new AttributeResource($attribute);

            return $this->sendResponse($attribute, 'Attribute restore successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('attributes-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $attribute = $this->repository->permanentDelete($id);

            return $this->sendResponse($attribute, 'Attribute permanently deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
