<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\SliderRepository;
use App\Http\Requests\Admin\SliderRequest;
use App\Http\Resources\Admin\SliderResource;
use App\Http\Resources\Admin\SliderCollection;

class SliderController extends BaseController
{
    public function __construct(protected SliderRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('sliders-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $sliders = $this->repository->index($request);

            $sliders = new SliderCollection($sliders);

            return $this->sendResponse($sliders, 'Slider list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(SliderRequest $request)
    {
        if (!$request->user()->hasPermission('sliders-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $slider = $this->repository->store($request);

            $slider = new SliderResource($slider);

            return $this->sendResponse($slider, 'Slider created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('sliders-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $slider = $this->repository->show($id);

            $slider = new SliderResource($slider);

            return $this->sendResponse($slider, 'Slider single view', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(SliderRequest $request, $id)
    {
        if (!$request->user()->hasPermission('sliders-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $slider = $this->repository->update($request, $id);

            $slider = new SliderResource($slider);

            return $this->sendResponse($slider, 'Slider updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('sliders-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $slider = $this->repository->delete($id);

            return $this->sendResponse($slider, 'Slider deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('sliders-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $sliders = $this->repository->trashList($request);

            $sliders = new SliderCollection($sliders);

            return $this->sendResponse($sliders, 'Slider trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('sliders-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $slider = $this->repository->restore($id);

            $slider = new SliderResource($slider);

            return $this->sendResponse($slider, 'Slider restore successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('sliders-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $slider = $this->repository->permanentDelete($id);

            return $this->sendResponse($slider, 'Slider permanently deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
