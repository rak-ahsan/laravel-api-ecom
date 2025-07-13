<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\ExpenseRepository;
use App\Http\Requests\Admin\ExpenseRequest;
use App\Http\Resources\Admin\ExpenseResource;
use App\Http\Resources\Admin\ExpenseCollection;

class ExpenseController extends BaseController
{
    public function __construct(protected ExpenseRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('expenses-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $expenses = $this->repository->index($request);

            $expenses = new ExpenseCollection($expenses);

            return $this->sendResponse($expenses, 'Expense list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(ExpenseRequest $request)
    {
        if (!$request->user()->hasPermission('expenses-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $expense = $this->repository->store($request);

            $expense = new ExpenseResource($expense);

            return $this->sendResponse($expense, 'Expense created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('expenses-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $expense = $this->repository->show($id);

            $expense = new ExpenseResource($expense);

            return $this->sendResponse($expense, "Expense single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(ExpenseRequest $request, $id)
    {
        if (!$request->user()->hasPermission('expenses-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $expense = $this->repository->update($request, $id);

            $expense = new ExpenseResource($expense);

            return $this->sendResponse($expense, 'Expense updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('expenses-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $expense = $this->repository->delete($id);

            return $this->sendResponse($expense, 'Expense deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('expenses-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $expenses = $this->repository->trashList($request);

            $expenses = new ExpenseCollection($expenses);

            return $this->sendResponse($expenses, 'Expense trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('expenses-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $expense = $this->repository->restore($id);

            $expense = new ExpenseResource($expense);

            return $this->sendResponse($expense, "Expense restore successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('expenses-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $expense = $this->repository->permanentDelete($id);

            return $this->sendResponse($expense, "Expense permanently deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
