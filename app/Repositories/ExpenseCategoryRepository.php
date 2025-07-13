<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Enums\StatusEnum;
use Illuminate\Support\Str;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class ExpenseCategoryRepository
{
    public function __construct(protected ExpenseCategory $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $expenses = $this->model->with(["createdBy:id,username"])
            ->when($searchKey, fn ($query) => $query->where("name", "like", "%$searchKey%"))
            ->orderBy('created_at', 'desc')->paginate($paginateSize);

            return $expenses;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $expenseCategory = new $this->model();

            $expenseCategory->name   = $request->name;
            $expenseCategory->slug   = Str::slug($request->name);
            $expenseCategory->status = $request->status ?? StatusEnum::ACTIVE->value;
            $expenseCategory->save();
            DB::commit();

            return $expenseCategory;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $expenseCategory = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$expenseCategory) {
                throw new CustomException("Expense Category not found");
            }

            return $expenseCategory;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $expenseCategory = $this->model->find($id);
            if (!$expenseCategory) {
                throw new CustomException("Expense Category Not found");
            }

            $expenseCategory->name   = $request->name;
            $expenseCategory->slug   = Str::slug($request->name);
            $expenseCategory->status = $request->status ?? StatusEnum::ACTIVE->value;
            $expenseCategory->save();

            DB::commit();

            return $expenseCategory;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $expenseCategory = $this->model->find($id);
            if (!$expenseCategory) {
                throw new CustomException("Expense Category not found");
            }

            return $expenseCategory->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $name         = $request->input('name', null);
        $slug         = $request->input("slug", null);

        try {
            $expenses = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($name, fn($query) => $query->where("name", "like", "%$name%"))
            ->when($slug, fn($query) => $query->where("slug", $slug))
            ->paginate($paginateSize);

            return $expenses;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $expenseCategory = ExpenseCategory::onlyTrashed()->find($id);
            if (!$expenseCategory) {
                throw new CustomException("Expense Category not found");
            }

            $expenseCategory->restore();

            return $expenseCategory;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $expenseCategory = ExpenseCategory::withTrashed()->find($id);
            if (!$expenseCategory) {
                throw new CustomException("Expense Category not found");
            }

            return $expenseCategory->forceDelete();

        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
