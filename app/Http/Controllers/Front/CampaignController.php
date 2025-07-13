<?php

namespace App\Http\Controllers\Front;


use Exception;
use Carbon\Carbon;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\CampaignRepository;
use App\Http\Resources\Front\CampaignResource;
use App\Http\Resources\Front\CampaignCollection;

class CampaignController extends BaseController
{
    public $repository;

    public function __construct(CampaignRepository $repository)
    {
        $this->repository = $repository;
    }

    function index(Request $request)
    {
        $now = Carbon::now();

        try {
            $request->merge(["now" => $now, "status" => StatusEnum::ACTIVE->value]);

            $campaigns = $this->repository->index($request);

            $campaigns = new CampaignCollection($campaigns);

            return $this->sendResponse($campaigns, "Campaign products", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show($id)
    {
        try {
            $campaign = $this->repository->show($id, "active");

            $campaign = new CampaignResource($campaign);

            return $this->sendResponse($campaign, "Campaign product single view", 200);

        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    // Get campaign product price
    function campaignProductPrice(Request $request)
    {
        try {
            $campaignProductPrice = $this->repository($request);

            return $this->sendResponse($campaignProductPrice, 'Variation product price', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

}
