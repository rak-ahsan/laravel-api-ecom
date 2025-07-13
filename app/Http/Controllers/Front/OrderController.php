<?php

namespace App\Http\Controllers\Front;

use Exception;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use Illuminate\Support\Facades\Log;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Auth;
use App\Repositories\OrderRepository;
use App\Http\Requests\Front\StoreOrderRequest;

class OrderController extends BaseController
{
    protected $repository;

    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $paginateSize = $request->input('paginate_size', null);
            $searchKey    = $request->input('search_key', null);
            $statusId     = $request->input('status_id', null);
            $isPaid       = $request->input('is_paid', '');
            $paginateSize = $this->checkPaginateSize($paginateSize);

            $orders = Order::with([
                'currentStatus:id,name', 'paymentGateway:id,name', 'deliveryGateway:id,name', 'items', 'items.productPrices:id,name',
            ])->where("user_id", Auth::id());

            $orders = $orders->when($statusId, fn ($query) => $query->where("status_id", $statusId))
                ->when($isPaid === 1, fn ($query) => $query->where("is_paid", 1))
                ->when($isPaid === 0, fn ($query) => $query->where("is_paid", 0))
                ->when($searchKey, function ($query) use ($searchKey) {
                    $query->where("id", $searchKey)
                        ->orWhere("phone_number", "like", "%$searchKey%")
                        ->orWhere("customer_name", "like", "%$searchKey%");
                });

            $orders = $orders->orderBy("created_at", "desc")->paginate($paginateSize);

            return $this->sendResponse($orders, "Order list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            $this->repository->customerOrder($request);

            if ($request->payment_gateway_id == 1) {
                return $this->sendResponse(null, 'Order submitted successfully', 200);
            } else if ($request->payment_gateway_id == 2) {
                return $this->sendResponse(null, 'Order submitted successfully', 200);
                // $trxId      = $order->id;
                // $amount     = $order->payable_price;
                // $numOfItems = count($orderDetails);
                // $response =  $this->executeSSLPayment($request->address_details, $request->customer_name, $phoneNumber, $amount, $trxId, $numOfItems);
                // if ($response) {
                //     return $this->sendResponse($response, "SSL redirect url");
                // } else {
                //     $this->sendError('Something is wrong');
                // }
            } else {
                return $this->sendResponse(null, 'Order submitted successfully', 200);
            }
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show($id)
    {
        try {
            $order = Order::with([
                'items', 'items.campaignProducts:id,name', 'statuses:id,name', 'paymentGateway:id,name'
            ])->where("user_id", Auth::id())->find($id);

            return $this->sendResponse($order, "Order single view", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
