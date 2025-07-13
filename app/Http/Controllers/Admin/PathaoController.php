<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Order;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\PathaoRepository;
use App\Http\Requests\Admin\PathaoCreateOrderRequest;
use App\Http\Requests\Admin\PathaoCreateStoreRequest;
use App\Http\Resources\Admin\PathaoEnvCredentialResource;
use App\Http\Requests\Admin\PathaoPriceCalculationRequest;
use App\Http\Requests\Admin\PathaoUpdateEnvCredentialRequest;

class PathaoController extends BaseController
{
    public function __construct(protected PathaoRepository $repository){}

    public function getCity()
    {
        try {
            $result = $this->repository->getCities();

            return $this->sendResponse($result, 'City list');
        } catch (Exception $exception ) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function getZone($cityId)
    {
        try {
            $result = $this->repository->getZones($cityId);

            return $this->sendResponse($result, 'Zone list');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function getArea($zoneId)
    {
        try {
            $result = $this->repository->getAreas($zoneId);

            return $this->sendResponse($result, 'Pathao area list');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function getStore()
    {
        try {
            $result = $this->repository->getStores();

            return $this->sendResponse($result, 'Store list');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }

    }

    public function createStore(PathaoCreateStoreRequest $request)
    {
        try {
            $result = $this->repository->createNewStore($request);

            if ($result['type'] == 'success') {
                return $this->sendResponse($result['message'], "Store created successfully");
            } else {
                $errorMessage = null;
                if (isset($result['errors']['name'])) {
                    $errorMessage = $result['errors']['name'][0];
                } elseif (isset($result['errors']['city_id'])) {
                    $errorMessage = $result['errors']['city_id'][0];
                } elseif (isset($result['errors']['zone_id'])) {
                    $errorMessage = $result['errors']['zone_id'][0];
                } elseif (isset($result['errors']['area_id'])) {
                    $errorMessage = $result['errors']['area_id'][0];
                } elseif (isset($result['errors']['contact_name'])) {
                    $errorMessage = $result['errors']['contact_name'][0];
                } elseif (isset($result['errors']['contact_number'])) {
                    $errorMessage = $result['errors']['contact_number'][0];
                } elseif (isset($result['errors']['address'])) {
                    $errorMessage = $result['errors']['address'][0];
                } else {
                    $errorMessage = 'Invalid information';
                }

                return $this->sendError($errorMessage);
            }
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderShipped($id)
    {
        try {
            $data = $this->repository->orderShipped($id);

            return $this->sendResponse($data, null);
        } catch (CustomException $exception) {
            Log::error($exception->getMessage());
            return $this->sendError($exception->getMessage());

        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function createOrder(PathaoCreateOrderRequest $request)
    {
        try {
            $result = $this->repository->createNewOrder($request);

            if ($result['type'] == 'success') {
                $courierPayable = 0;
                $trackingNumber = null;

                if (isset($result['data']['consignment_id'])) {
                    $trackingNumber = $result['data']['consignment_id'];
                }

                if (isset($result['data']['delivery_fee'])) {
                    $courierPayable = $result['data']['delivery_fee'];
                }

                $order = Order::find($request->order_id);
                if ($order) {
                    $order->courier_payable = $courierPayable;
                    $order->tracking_code   = $trackingNumber;
                    $order->courier_name    = 'Pathao';
                    $order->save();
                }

                return $this->sendResponse($result['message'], null);

            } else {
                $errorMessage = null;
                if (isset($result['errors']['store_id'])) {
                    $errorMessage = $result['errors']['store_id'][0];
                } elseif (isset($result['errors']['recipient_name'])) {
                    $errorMessage = $result['errors']['recipient_name'][0];
                } elseif (isset($result['errors']['recipient_phone'])) {
                    $errorMessage = $result['errors']['recipient_phone'][0];
                } elseif (isset($result['errors']['sender_name'])) {
                    $errorMessage = $result['errors']['sender_name'][0];
                } elseif (isset($result['errors']['sender_phone'])) {
                    $errorMessage = $result['errors']['sender_phone'][0];
                } elseif (isset($result['errors']['recipient_city'])) {
                    $errorMessage = $result['errors']['recipient_city'][0];
                } elseif (isset($result['errors']['recipient_zone'])) {
                    $errorMessage = $result['errors']['recipient_zone'][0];
                } elseif (isset($result['errors']['recipient_address'])) {
                    $errorMessage = $result['errors']['recipient_address'][0];
                } elseif (isset($result['errors']['amount_to_collect'])) {
                    $errorMessage = $result['errors']['amount_to_collect'][0];
                } elseif (isset($result['errors']['item_weight'])) {
                    $errorMessage = $result['errors']['item_weight'][0];
                } elseif (isset($result['errors']['item_type'])) {
                    $errorMessage = $result['errors']['item_type'][0];
                } elseif (isset($result['errors']['delivery_type'])) {
                    $errorMessage = $result['errors']['delivery_type'][0];
                } elseif (isset($result['errors']['item_quantity'])) {
                    $errorMessage = $result['errors']['item_quantity'][0];
                } else {
                    $errorMessage = 'Invalid information';
                }

                return $this->sendError($errorMessage);
            }
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function costCalculation(PathaoPriceCalculationRequest $request)
    {
        $result = $this->repository->priceCalculation($request);

        if ($result["type"] === 'error') {
            return $this->sendError($result);
        } else {
            return $this->sendResponse($result, 'Cost calculation');
        }
    }

    public function updateEnvCredential(PathaoUpdateEnvCredentialRequest $request)
    {
        if (!$request->user()->hasPermission('pathao-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $res = $this->repository->updateEnvCredential($request);

            return $this->sendResponse($res, 'Credential updated successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show()
    {
        try {
            $pathao = $this->repository->show();

            $pathao = new PathaoEnvCredentialResource($pathao);

            return $this->sendResponse($pathao, "Pathao credentials");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
