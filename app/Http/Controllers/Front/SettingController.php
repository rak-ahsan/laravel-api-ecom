<?php

namespace App\Http\Controllers\Front;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\SettingRepository;
use App\Http\Resources\Front\SettingResource;
use App\Http\Resources\Front\SettingCollection;

class SettingController extends BaseController
{
    protected $repository;

    public function __construct(SettingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE->value]);

            $settings = $this->repository->index($request);

            $settings = new SettingCollection($settings);

            return $this->sendResponse($settings, "Setting list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE->value]);

            $setting = $this->repository->show($id);

            $setting = new SettingResource($setting);

            return $this->sendResponse($setting, "Setting single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
