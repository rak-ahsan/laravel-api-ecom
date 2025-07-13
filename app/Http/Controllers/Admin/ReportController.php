<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use Illuminate\Support\Facades\Log;
use App\Repositories\ReportRepository;
use App\Http\Resources\Admin\OrderCollection;
use App\Http\Requests\Admin\OrderReportRequest;

class ReportController extends BaseController
{
    public function __construct(protected ReportRepository $repository){}

    public function orderReport(OrderReportRequest $request)
    {
        if (!$request->user()->hasPermission('reports-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders = $this->repository->orderReport($request);

            $orders = new OrderCollection($orders);

            return $this->sendResponse($orders, 'Order report', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderReportMonthly(Request $request)
    {
        if (!$request->user()->hasPermission('reports-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders = $this->repository->orderReportMonthly($request);

            return $this->sendResponse($orders, 'Monthly order report', 200);

        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderReportYearly(Request $request)
    {
        if (!$request->user()->hasPermission('reports-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders  = $this->repository->orderReportYearly();

            return $this->sendResponse($orders, 'Yearly order report', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderReportByLocation(Request $request)
    {
        if (!$request->user()->hasPermission('reports-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders = $this->repository->orderReportByLocation($request);

            return $this->sendResponse($orders, 'Order report by location', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderReportBySelling(Request $request)
    {
        if (!$request->user()->hasPermission('reports-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders = $this->repository->orderReportBySelling($request);

            return $this->sendResponse($orders, 'Order report by selling', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderReportByCustomer(Request $request)
    {
        if (!$request->user()->hasPermission('reports-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders = $this->repository->orderReportByCustomer($request);

            return $this->sendResponse($orders, 'Order report by customer', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
