<?php

namespace App\Repositories;

use Exception;
use App\Models\Expense;
use App\Classes\Helper;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class ExpenseRepository
{
    public function __construct(protected Expense $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $paginateSize = $request->input("paginate_size", null);
        $categoryId   = $request->input("category_id", null);
        $startDate    = $request->input("start_date", null);
        $endDate      = $request->input("end_date", null);
        $endDate      = $endDate ? $endDate : $startDate;

        try {
            $expenses = $this->model->with("category:id,name")
            ->when(($startDate && $endDate), fn ($query) => $query->whereBetween("expense_date", [$startDate, $endDate]))
            ->when($categoryId, fn($query) => $query->where('category_id', $categoryId))
            ->orderBy("created_at", "desc")
            ->paginate($paginateSize);

            return $expenses;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $expense = new $this->model();

            $expense->category_id  = $request->category_id;
            $expense->amount       = $request->amount;
            $expense->description  = $request->description;
            $expense->expense_date = $request->expense_date;
            $expense->save();

            DB::commit();

            return $expense;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $expense = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);
            if (!$expense) {
                throw new CustomException("Expense not found");
            }
            return $expense;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $expense = $this->model->find($id);
            if (!$expense) {
                throw new CustomException("Expense Not found");
            }

            $expense->category_id  = $request->category_id;
            $expense->amount       = $request->amount;
            $expense->description  = $request->description;
            $expense->expense_date = $request->expense_date;
            $expense->save();

            DB::commit();

            return $expense;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $expense = $this->model->find($id);
            if (!$expense) {
                throw new CustomException("Expense not found");
            }

            return $expense->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $paginateSize = $request->input("paginate_size", null);
        $categoryId   = $request->input("category_id", null);
        $startDate    = $request->input("start_date", null);
        $endDate      = $request->input("end_date", null);
        $endDate      = $endDate ? $endDate : $startDate;

        try {
            $expenses = $this->model->with("category:id,name")
            ->onlyTrashed()
            ->when(($startDate && $endDate), fn ($query) => $query->whereBetween("expense_date", [$startDate, $endDate]))
            ->when($categoryId, fn($query) => $query->where('category_id', $categoryId))
            ->orderBy("created_at", "desc")
            ->paginate($paginateSize);

            return $expenses;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $expense = $this->model->onlyTrashed()->find($id);
            if (!$expense) {
                throw new CustomException("Expense not found");
            }

            $expense->restore();

            return $expense;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $expense = $this->model->withTrashed()->find($id);
            if (!$expense) {
                throw new CustomException("Expense not found");
            }

            return $expense->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
