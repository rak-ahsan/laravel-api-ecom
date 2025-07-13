<?php

namespace App\Repositories;

use Exception;
use App\Models\FAQ;
use App\Classes\Helper;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class FAQRepository
{
    public function __construct(protected FAQ $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $status       = $request->input('status', null);

        try {
            $faqs = $this->model->with(["createdBy:id,username"])
            ->when($status, fn($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $faqs;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $faq = new $this->model();

            $faq->question = $request->question;
            $faq->answer   = $request->answer;
            $faq->status   = $request->status;
            $faq->save();

            DB::commit();

            return $faq;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $faq = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$faq) {
                throw new CustomException("FAQ not found");
            }

            return $faq;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $faq = $this->model->find($id);
            if (!$faq) {
                throw new CustomException("FAQ not found");
            }

            $faq->question = $request->question;
            $faq->answer   = $request->answer;
            $faq->status   = $request->status;
            $faq->save();

            DB::commit();

            return $faq;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $faq = $this->model->find($id);
            if (!$faq) {
                throw new CustomException("FAQ not found");
            }

            return $faq->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $status       = $request->input('status', null);

        try {
            $faqs = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($status, fn($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);
        return $faqs;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $faq = $this->model->onlyTrashed()->find($id);
            if (!$faq) {
                throw new CustomException("FAQ not found");
            }

            $faq->restore();

            return $faq;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $faq = $this->model->withTrashed()->find($id);
            if (!$faq) {
                throw new CustomException("FAQ not found");
            }

            return $faq->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
