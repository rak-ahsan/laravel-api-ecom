<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\SectionRepository;
use App\Http\Requests\Admin\SectionRequest;
use App\Http\Resources\Admin\SectionResource;
use App\Http\Resources\Admin\SectionCollection;

class SectionController extends BaseController
{
    public function __construct(protected SectionRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission("sections-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $sections = $this->repository->index($request);

            $sections = new SectionCollection($sections);

            return $this->sendResponse($sections, "Section list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(SectionRequest $request)
    {
        if (!$request->user()->hasPermission("sections-create")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $section = $this->repository->store($request);

            $section = new SectionResource($section);

            return $this->sendResponse($section, "Section created successfully", 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission("sections-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $section = $this->repository->show($id);

            $section = new SectionResource($section);

            return $this->sendResponse($section, "Section single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(SectionRequest $request, $id)
    {
        if (!$request->user()->hasPermission("sections-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $section = $this->repository->update($request, $id);

            $section = new SectionResource($section);

            return $this->sendResponse($section, "Section updated successfully", 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission("sections-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $section = $this->repository->delete($id);

            return $this->sendResponse($section, "Section deleted successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission("sections-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $sections = $this->repository->trashList($request);

            $sections = new SectionCollection($sections);

            return $this->sendResponse($sections, "Section trash list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission("sections-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $section = $this->repository->restore($id);

            $section = new SectionResource($section);

            return $this->sendResponse($section, "Section restore successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission("sections-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $section = $this->repository->permanentDelete($id);

            return $this->sendResponse($section, "Section permanently deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
