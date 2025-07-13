<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SocialMediaRequest;
use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\SocialMediaRepository;
use App\Http\Resources\Admin\SocialMediaResource;
use App\Http\Resources\Admin\SocialMediaCollection;

class SocialMediaController extends BaseController
{
    public function __construct(protected SocialMediaRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('social-medias-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $socialMedias = $this->repository->index($request);

            $socialMedias = new SocialMediaCollection($socialMedias);

            return $this->sendResponse($socialMedias, 'Social media list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(SocialMediaRequest $request)
    {
        if (!$request->user()->hasPermission('social-medias-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $socialMedia = $this->repository->store($request);

            $socialMedia = new SocialMediaResource($socialMedia);

            return $this->sendResponse($socialMedia, 'Social media created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('social-medias-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
             $socialMedia = $this->repository->show($id);

             $socialMedia = new SocialMediaResource($socialMedia);

            return $this->sendResponse($socialMedia, "Social media single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(SocialMediaRequest $request, $id)
    {
        if (!$request->user()->hasPermission('social-medias-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $socialMedia = $this->repository->update($request, $id);

            $socialMedia = new SocialMediaResource($socialMedia);

            return $this->sendResponse($socialMedia, 'Social media updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);

        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('social-medias-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $socialMedia = $this->repository->delete($id);

            return $this->sendResponse($socialMedia, 'Social media deleted successfully', 200);
        } catch (CustomException $exception) {

          return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('social-medias-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $socialMedias = $this->repository->trashList($request);

            $socialMedias = new SocialMediaCollection($socialMedias);

            return $this->sendResponse($socialMedias, 'Social media trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('social-medias-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $socialMedia = $this->repository->restore($id);

            $socialMedia = new SocialMediaResource($socialMedia);

            return $this->sendResponse($socialMedia, "Social media restore successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('social-medias-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $socialMedia = $this->repository->permanentDelete($id);

            return $this->sendResponse($socialMedia, "Social media permanently deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
