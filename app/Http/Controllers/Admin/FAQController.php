<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use App\Repositories\FAQRepository;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\FAQRequest;
use App\Http\Resources\Admin\FAQResource;
use App\Http\Resources\Admin\FAQCollection;

class FAQController extends BaseController
{
    public function __construct(protected FAQRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('faqs-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $faqs = $this->repository->index($request);

            $faqs = new FAQCollection($faqs);

            return $this->sendResponse($faqs, 'FAQ list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(FAQRequest $request)
    {
        if (!$request->user()->hasPermission('faqs-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $faq = $this->repository->store($request);

            $faq = new FAQResource($faq);

            return $this->sendResponse($faq, 'FAQ created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('faqs-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $faq = $this->repository->show($id);

            $faq = new FAQResource($faq);

            return $this->sendResponse($faq, 'FAQ single view', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(FAQRequest $request, $id)
    {
        if (!$request->user()->hasPermission('faqs-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $faq = $this->repository->update($request, $id);

            $faq = new FAQResource($faq);

            return $this->sendResponse($faq, 'FAQ updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function delete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('faqs-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $faq = $this->repository->delete($id);

            return $this->sendResponse($faq, 'FAQ deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('faqs-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $faqs = $this->repository->trashList($request);

            $faqs = new FAQCollection($faqs);

            return $this->sendResponse($faqs, 'FAQ trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('faqs-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $faq = $this->repository->restore($id);

            $faq = new FAQResource($faq);

            return $this->sendResponse($faq, 'FAQ restore successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('faqs-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $faq = $this->repository->permanentDelete($id);

            return $this->sendResponse($faq, 'FAQ permanently deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
