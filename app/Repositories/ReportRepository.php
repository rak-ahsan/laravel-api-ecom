<?php

namespace App\Repositories;

use Exception;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class ReportRepository
{
    public function __construct(protected Order $model){}

    public function orderReport($request)
    {
        $startDate = $request->input('start_date', now());
        $endDate   = $request->input('end_date', now());

        try {
            $data = [];
            $order = $this->model->with(['paymentGateway:id,name', 'deliveryGateway:id,name', 'currentStatus:id,name'])->where("paid_status", "paid");

            if ($startDate && $endDate) {
                $startDate = Carbon::parse($startDate)->startOfDay();
                $endDate   = Carbon::parse($endDate)->endOfDay();
                $order     = $order->whereBetween('created_at', [$startDate, $endDate]);
                $data      = $order->orderBy('created_at', 'desc')->get();
            }

            return $data;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function orderReportMonthly($request)
    {
        $currentMonth = Carbon::now()->format('F');
        $daysInMonth  = Carbon::now()->daysInMonth;

        try {
            $orders = $this->model->select(
                DB::raw('GROUP_CONCAT(DATE(created_at)) as order_dates'),
                DB::raw('COUNT(id) as order_count')
            )
            ->whereMonth('created_at', '=', Carbon::now()->month)
            ->orderBy('order_dates')
            ->get();

            // Explode the concatenated string into an array
            $orderDatesArray = explode(',', $orders->first()->order_dates);

            // Create an array with days of the month and initialize order counts to 0
            $daysArray = [];
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $daysArray[$day] = 0;
            }

            // Fill in the order counts for each day
            foreach ($orderDatesArray as $orderDate) {
                $day = Carbon::parse($orderDate)->format('j'); // Format day as a string
                $daysArray[$day] += 1; // Increment the count for the specific day
            }

            // Output the result
            $result = [];
            foreach ($daysArray as $orderDate => $orderCount) {
                $result[] = [
                    'order_date'  => $orderDate,
                    'order_count' => $orderCount,
                ];
            }

            $data = [
                "current_month" => $currentMonth,
                "data"          => $result
            ];

            return $data;

        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function orderReportYearly()
    {
        $currentYear = Carbon::now()->year;

        try {
            $orders = $this->model->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(id) as order_count')
            )
                ->whereYear('created_at', '=', $currentYear)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Initialize an array with counts for each month
            $monthsArray = array_fill(1, 12, 0);

            // Fill in the order counts for each month
            foreach ($orders as $orderCount) {
                $month = $orderCount->month;
                $count = $orderCount->order_count;
                $monthsArray[$month] = $count;
            }

            // Output the result
            $result = [];
            foreach ($monthsArray as $month => $orderCount) {
                $result[] = [
                    'month' => date('F', mktime(0, 0, 0, $month, 1)), // Format month as full month name
                    'order_count' => $orderCount,
                ];
            }

            $data = [
                "current_year" => $currentYear,
                "data" => $result
            ];

            return $data;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function orderReportByLocation($request)
    {
        $limit = $request->input('limit', 10);

        try {
            $orders = $this->model->select('district')
                ->selectRaw('COUNT(*) as order_count')
                ->groupBy('district')
                ->take($limit)
                ->get();

            return $orders;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function orderReportBySelling($request)
    {
        $limit = $request->input('limit', 10);

        try {
            $orders = $this->model->select('products.id', 'products.name', DB::raw('COUNT(orders.id) as order_count'))
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->join('products', 'order_details.product_id', '=', 'products.id')
                ->groupBy('products.id', 'products.name')
                ->take($limit)
                ->get();

            return $orders;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function orderReportByCustomer($request)
    {

        $limit = $request->input('limit', 10);

        try {
            $orders = $this->model->select('customer_name', 'phone_number', DB::raw('COUNT(id) as order_count'), DB::raw('SUM(net_order_price) as order_value'))
                ->groupBy('customer_name', 'phone_number')
                ->take($limit)
                ->get();

            return $orders;
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}







