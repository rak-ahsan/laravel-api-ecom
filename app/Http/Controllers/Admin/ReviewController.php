<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\ReviewRepository;
use App\Http\Requests\Admin\ReviewRequest;
use App\Http\Resources\Admin\ReviewResource;
use App\Http\Resources\Admin\ReviewCollection;

class ReviewController extends BaseController
{
    public function __construct(protected ReviewRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission("reviews-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $reviews = $this->repository->index($request);

            $reviews = new ReviewCollection($reviews);

            return $this->sendResponse($reviews, "Review list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(ReviewRequest $request)
    {
        if (!$request->user()->hasPermission("reviews-create")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $review = $this->repository->store($request);

            $review = new ReviewResource($review);

            return $this->sendResponse($review, "Review created successfully", 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission("reviews-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $review = $this->repository->show($id);

            $review = new ReviewResource($review);

            return $this->sendResponse($review, "Review single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(ReviewRequest $request, $id)
    {
        if (!$request->user()->hasPermission("reviews-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $review = $this->repository->update($request, $id);

            $review = new ReviewResource($review);

            return $this->sendResponse($review, "Review updated successfully", 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission("reviews-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $review = $this->repository->delete($id);

            return $this->sendResponse($review, "Review deleted successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission("reviews-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $review = $this->repository->trashList($request);

            $review = new ReviewCollection($review);

            return $this->sendResponse($review, "Review trash list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission("reviews-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $review = $this->repository->restore($id);

            $review = new ReviewResource($review);

            return $this->sendResponse($review, "Review restore successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission("reviews-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $review = $this->repository->permanentDelete($id);

            return $this->sendResponse($review, "Review permanently deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
