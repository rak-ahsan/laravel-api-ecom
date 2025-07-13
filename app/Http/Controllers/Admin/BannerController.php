<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\BannerRepository;
use App\Http\Requests\Admin\BannerRequest;
use App\Http\Resources\Admin\BannerResource;
use App\Http\Resources\Admin\BannerCollection;


class BannerController extends BaseController
{
    public function __construct(protected BannerRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('banners-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $banners = $this->repository->index($request);

            $banners = new BannerCollection($banners);

            return $this->sendResponse($banners, 'Banner list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('banners-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $banner = $this->repository->show($id);

            $banner = new BannerResource($banner);

            return $this->sendResponse($banner, 'Banner single view', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(BannerRequest $request)
    {
        if (!$request->user()->hasPermission('banners-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $banner = $this->repository->store($request);

            $banner = new BannerResource($banner);

            return $this->sendResponse($banner, 'Banner created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(BannerRequest $request, $id)
    {
        if (!$request->user()->hasPermission('banners-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $banner = $this->repository->update($request, $id);

            $banner = new BannerResource($banner);

            return $this->sendResponse($banner, 'Banner updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('banners-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $banner = $this->repository->delete($id);

            return $this->sendResponse($banner, 'Banner deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('banners-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $banners = $this->repository->trashList($request);

            $banners = new BannerCollection($banners);

            return $this->sendResponse($banners, 'Banner trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('banners-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $banner = $this->repository->restore($id);

            $banner = new BannerResource($banner);

            return $this->sendResponse($banner, 'Banner restore successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('banners-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $banner = $this->repository->permanentDelete($id);

            return $this->sendResponse($banner, 'Banner permanently deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
