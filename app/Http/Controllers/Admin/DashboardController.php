<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseController
{
    public function dashboard(Request $request)
    {
        if (!$request->user()->hasPermission('dashboards-read')) {
            return $this->sendError(__("Common.unauthorized"));
        }

        $totalUsers = User::count();

        // Current order report
        $orderReport = Order::select(DB::raw('
            count(*) as order_count,
            SUM(payable_price) as order_value,
            SUM(CASE WHEN is_paid = 1 THEN 1 ELSE 0 END) as paid_order,
            SUM(CASE WHEN is_paid = 1 THEN payable_price ELSE 0 END) as paid_order_value,
            SUM(CASE WHEN is_paid = 0 THEN 1 ELSE 0 END) as unpaid_order,
            SUM(CASE WHEN is_paid = 0 THEN payable_price ELSE 0 END) as unpaid_order_value,
            SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) as submitted_order,
            SUM(CASE WHEN status_id = 1 THEN payable_price ELSE 0 END) as submitted_order_value,
            SUM(CASE WHEN status_id = 2 THEN 1 ELSE 0 END) as confirm_order,
            SUM(CASE WHEN status_id = 2 THEN payable_price ELSE 0 END) as confirm_order_value,
            SUM(CASE WHEN status_id = 3 THEN 1 ELSE 0 END) as canceled_order,
            SUM(CASE WHEN status_id = 3 THEN payable_price ELSE 0 END) as canceled_order_value,
            SUM(CASE WHEN status_id = 6 THEN 1 ELSE 0 END) as delivered_order,
            SUM(CASE WHEN status_id = 6 THEN payable_price ELSE 0 END) as delivered_order_value,
            SUM(CASE WHEN status_id = 8 THEN 1 ELSE 0 END) as returned_order,
            SUM(CASE WHEN status_id = 8 THEN payable_price ELSE 0 END) as returned_order_value
        '))->first();

        $data = [
            'total_users'  => $totalUsers,
            'order_report' => $orderReport,
        ];

        return $this->sendResponse($data, 'Dashboard information');
    }
}
