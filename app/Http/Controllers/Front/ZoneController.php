<?php

namespace App\Http\Controllers\Front;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\ZoneRepository;
use App\Http\Resources\Front\ZoneResource;
use App\Http\Resources\Front\ZoneCollection;

class ZoneController extends BaseController
{
    protected $repository;
    public function __construct(ZoneRepository $repository)
    {
        $this->repository = $repository;
    }
    public function index(Request $request)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE->value]);

            $zones = $this->repository->index($request);

            $zones = new ZoneCollection($zones);

            return $this->sendResponse($zones, "Zone list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE->value]);

            $zone = $this->repository->show($id);

            $zone = new ZoneResource($zone);

            return $this->sendResponse($zone, "Zone single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
