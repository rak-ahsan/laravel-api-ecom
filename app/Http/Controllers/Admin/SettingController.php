<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\SettingRepository;
use App\Http\Requests\Admin\SettingRequest;
use App\Http\Resources\Admin\SettingResource;
use App\Http\Resources\Admin\SettingCollection;

class SettingController extends BaseController
{
    public function __construct(protected SettingRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('settings-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $settings = $this->repository->index($request);

            $settings = new SettingCollection($settings);

            return $this->sendResponse($settings, 'Setting list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('settings-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $setting = $this->repository->show($id);

            $setting = new SettingResource($setting);

            return $this->sendResponse($setting, 'Setting single view', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(SettingRequest $request)
    {
        if (!$request->user()->hasPermission('settings-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $setting = $this->repository->store($request);

            $setting = new SettingResource($setting);

            return $this->sendResponse($setting, 'Setting created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(SettingRequest $request, $id)
    {
        if (!$request->user()->hasPermission('settings-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $setting = $this->repository->update($request, $id);

            $setting = new SettingResource($setting);

            return $this->sendResponse($setting, 'Setting updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('settings-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $setting = $this->repository->delete($id);

            return $this->sendResponse($setting, 'Setting deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('settings-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $settings = $this->repository->trashList($request);

            $settings = new SettingCollection($settings);

            return $this->sendResponse($settings, 'Setting trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('settings-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $setting = $this->repository->restore($id);

            $setting = new SettingResource($setting);

            return $this->sendResponse($setting, 'Setting restore successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('settings-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $setting = $this->repository->permanentDelete($id);

            return $this->sendResponse($setting, 'Setting permanently deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
