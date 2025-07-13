<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\ContactRepository;
use App\Http\Requests\Admin\ContactRequest;
use App\Http\Resources\Admin\ContactResource;
use App\Http\Resources\Admin\ContactCollection;

class ContactController extends BaseController
{
    public function __construct(protected ContactRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('contacts-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $contact = $this->repository->index($request);

            $contact = new ContactCollection($contact);

            return $this->sendResponse($contact, 'Contact list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('contacts-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $contact = $this->repository->show($id);

            $contact = new ContactResource($contact);

            return $this->sendResponse($contact, 'Contact single view', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(ContactRequest $request)
    {
        if (!$request->user()->hasPermission('contacts-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $contact = $this->repository->store($request);

            $contact = new ContactResource($contact);

            return $this->sendResponse($contact, 'Contact created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(ContactRequest $request, $id)
    {
        if (!$request->user()->hasPermission('contacts-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $contact = $this->repository->update($request, $id);

            $contact = new ContactResource($contact);

            return $this->sendResponse($contact, 'Contact updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('contacts-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $contact = $this->repository->delete($id);

            return $this->sendResponse($contact, 'Contact deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('contacts-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $contact = $this->repository->trashList($request);

            $contact = new ContactCollection($contact);

            return $this->sendResponse($contact, 'Contact trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('contacts-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $contact = $this->repository->restore($id);

            $contact = new ContactResource($contact);

            return $this->sendResponse($contact, 'Contact restore successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('contacts-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $contact = $this->repository->permanentDelete($id);

            return $this->sendResponse($contact, 'Contact permanently deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
