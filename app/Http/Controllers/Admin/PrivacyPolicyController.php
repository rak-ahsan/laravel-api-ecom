<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\PrivacyPolicyRepository;
use App\Http\Requests\Admin\PrivacyPolicyRequest;
use App\Http\Resources\Admin\PrivacyPolicyResource;
use App\Http\Resources\Admin\PrivacyPolicyCollection;

class PrivacyPolicyController extends BaseController
{
    public function __construct(protected PrivacyPolicyRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('privacy-policies-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $Privacies = $this->repository->index($request);

            $Privacies = new PrivacyPolicyCollection($Privacies);

            return $this->sendResponse($Privacies, 'Privacy policy list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(PrivacyPolicyRequest $request)
    {
        if (!$request->user()->hasPermission('privacy-policies-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $Privacy = $this->repository->store($request);

            $Privacy = new PrivacyPolicyResource($Privacy);

            return $this->sendResponse($Privacy, 'Privacy policy created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('privacy-policies-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $Privacy = $this->repository->show($id);

            $Privacy = new PrivacyPolicyResource($Privacy);

            return $this->sendResponse($Privacy, 'Privacy policy single view', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(PrivacyPolicyRequest $request, $id)
    {
        if (!$request->user()->hasPermission('privacy-policies-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $Privacy = $this->repository->update($request, $id);

            $Privacy = new PrivacyPolicyResource($Privacy);

            return $this->sendResponse($Privacy, 'Privacy policy updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function delete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('privacy-policies-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $Privacy = $this->repository->delete($id);

            return $this->sendResponse($Privacy, 'Privacy policy deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('privacy-policies-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $Privacies = $this->repository->trashList($request);

            $Privacies = new PrivacyPolicyCollection($Privacies);

            return $this->sendResponse($Privacies, 'Privacy policy trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('privacy-policies-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $Privacy = $this->repository->restore($id);

            $Privacy = new PrivacyPolicyResource($Privacy);

            return $this->sendResponse($Privacy, 'Privacy policy restore successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('privacy-policies-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $Privacy = $this->repository->permanentDelete($id);

            return $this->sendResponse($Privacy, 'Privacy policy permanently deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
