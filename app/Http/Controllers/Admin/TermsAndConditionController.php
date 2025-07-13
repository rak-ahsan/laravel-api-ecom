<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\TermsAndConditionRepository;
use App\Http\Requests\Admin\TermsAndConditionRequest;
use App\Http\Resources\Admin\TermsAndConditionResource;
use App\Http\Resources\Admin\TermsAndConditionCollection;

class TermsAndConditionController extends BaseController
{
    public function __construct(protected TermsAndConditionRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('terms-and-conditions-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $terms = $this->repository->index($request);

            $terms = new TermsAndConditionCollection($terms);

            return $this->sendResponse($terms, 'Terms and condition list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(TermsAndConditionRequest $request)
    {
        if (!$request->user()->hasPermission('terms-and-conditions-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $term = $this->repository->store($request);

            $term = new TermsAndConditionResource($term);

            return $this->sendResponse($term, 'Terms and condition created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('terms-and-conditions-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $term = $this->repository->show($id);

            $term = new TermsAndConditionResource($term);

            return $this->sendResponse($term, 'Terms and condition single view', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(TermsAndConditionRequest $request, $id)
    {
        if (!$request->user()->hasPermission('terms-and-conditions-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $term = $this->repository->update($request, $id);

            $term = new TermsAndConditionResource($term);

            return $this->sendResponse($term, 'Terms and condition updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function delete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('terms-and-conditions-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $term = $this->repository->delete($id);

            return $this->sendResponse($term, 'Terms and condition deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('terms-and-conditions-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $terms = $this->repository->trashList($request);

            $terms = new TermsAndConditionCollection($terms);

            return $this->sendResponse($terms, 'Terms and condition trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('terms-and-conditions-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $term = $this->repository->restore($id);

            $term = new TermsAndConditionResource($term);

            return $this->sendResponse($term, 'Terms and condition restore successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('terms-and-conditions-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $term = $this->repository->permanentDelete($id);

            return $this->sendResponse($term, 'Terms and condition permanently deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
