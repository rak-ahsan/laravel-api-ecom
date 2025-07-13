<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\CampaignRepository;
use App\Http\Requests\Admin\CampaignRequest;
use App\Http\Resources\Admin\CampaignResource;
use App\Http\Resources\Admin\CampaignCollection;

class CampaignController extends BaseController
{
    public function __construct(protected CampaignRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('campaigns-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $campaign = $this->repository->index($request);

            $campaign = new CampaignCollection($campaign);

            return $this->sendResponse($campaign, "Campaign products list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('campaigns-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $campaign = $this->repository->show($id);

            $campaign = new CampaignResource($campaign);

            return $this->sendResponse($campaign, "Campaign products view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(CampaignRequest $request)
    {
        if (!$request->user()->hasPermission('campaigns-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $campaign = $this->repository->store($request);

            $campaign = new CampaignResource($campaign);

            return $this->sendResponse($campaign, "Campaign created successfully", 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(CampaignRequest $request, $id)
    {
        if (!$request->user()->hasPermission('campaigns-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $campaign = $this->repository->update($request, $id);

            $campaign = new CampaignResource($campaign);

            return $this->sendResponse(null, "Campaign updated successfully", 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('campaigns-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $campaign = $this->repository->delete($id);

            return $this->sendResponse($campaign, 'Campaign deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('campaigns-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $campaign = $this->repository->trashList($request);

            $campaign = new CampaignCollection($campaign);

            return $this->sendResponse($campaign, "Campaign products trash list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('campaigns-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $campaign = $this->repository->restore($id);

            $campaign = new CampaignResource($campaign);

            return $this->sendResponse($campaign, "Campaign restore successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('campaigns-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $campaign = $this->repository->permanentDelete($id);


            return $this->sendResponse($campaign, "Campaign products permanently deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
