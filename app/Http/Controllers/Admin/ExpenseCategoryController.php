<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\ExpenseCategoryRepository;
use App\Http\Requests\Admin\ExpenseCategoryRequest;
use App\Http\Resources\Admin\ExpenseCategoryResource;
use App\Http\Resources\Admin\ExpenseCategoryCollection;

class ExpenseCategoryController extends BaseController
{
    public function __construct(protected ExpenseCategoryRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('expense-categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $expenses = $this->repository->index($request);

            $expenses = new ExpenseCategoryCollection($expenses);

            return $this->sendResponse($expenses, 'Expense category list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(ExpenseCategoryRequest $request)
    {
        if (!$request->user()->hasPermission('expense-categories-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $expenseCategory = $this->repository->store($request);

            $expenseCategory = new ExpenseCategoryResource($expenseCategory);

            return $this->sendResponse($expenseCategory, 'Expense category created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('expense-categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $expenseCategory = $this->repository->show($id);

            $expenseCategory = new ExpenseCategoryResource($expenseCategory);

            return $this->sendResponse($expenseCategory, "Expense category single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(ExpenseCategoryRequest $request, $id)
    {
        if (!$request->user()->hasPermission('expense-categories-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $expenseCategory = $this->repository->update($request, $id);

            $expenseCategory = new ExpenseCategoryResource($expenseCategory);

            return $this->sendResponse($expenseCategory, 'Expense category updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            DB::rollback();

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('expense-categories-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $expenseCategory = $this->repository->delete($id);

            return $this->sendResponse($expenseCategory, 'Expense category deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('expense-categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $expenses = $this->repository->trashList($request);

            $expenses = new ExpenseCategoryCollection($expenses);

            return $this->sendResponse($expenses, 'Expense category trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('expense-categories-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $expenseCategory = $this->repository->restore($id);

            $expenseCategory = new ExpenseCategoryResource($expenseCategory);

            return $this->sendResponse($expenseCategory, 'Expense category restore successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('expense-categories-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $expenseCategory = $this->repository->permanentDelete($id);

            return $this->sendResponse($expenseCategory, "Expense category permanently deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
