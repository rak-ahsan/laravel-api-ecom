<?php

namespace App\Repositories;

use Exception;
use App\Models\Order;
use App\Models\Pathao;
use App\Classes\Helper;;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PathaoRepository
{
    private $endPoint;
    private $clientId;
    private $clientSecret;
    private $username;
    private $password;
    private $grantType;
    private $accessToken;
    private $refreshToken;

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $this->endPoint     = config("pathao.endpoint");
        $this->clientId     = config("pathao.client_id");
        $this->clientSecret = config("pathao.client_secret");
        $this->username     = config("pathao.username");
        $this->password     = config("pathao.password");
        $this->grantType    = config("pathao.grant_type");

        $data = $this->getToken();

        if (array_key_exists("access_token", $data) && array_key_exists("refresh_token", $data)) {
            $this->accessToken  = $data['access_token'];
            $this->refreshToken = $data['refresh_token'];
        }
    }

    // Get access token
    public function getToken()
    {
        try {
            $url = "{$this->endPoint}/aladdin/api/v1/issue-token";

            $headers = [
                "Accept"       => "application/json",
                "Content-Type" => "application/json"
            ];

            $body = [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'username'      => $this->username,
                'password'      => $this->password,
                'grant_type'    => $this->grantType
            ];

            $res = Http::withHeaders($headers)->post($url, $body);

            return json_decode($res, true);

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    // Get refresh token
    public function refreshToken()
    {
        try {
            $url = "{$this->endPoint}/aladdin/api/v1/issue-token";

            $headers = [
                "Accept"       => "application/json",
                "Content-Type" => "application/json"
            ];

            $body = [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $this->refreshToken,
                'grant_type'    => "refresh_token"
            ];

            $res = Http::withHeaders($headers)->post($url, $body);

            return json_decode($res, true);

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function createNewOrder($request)
    {
        try {
            $url = "{$this->endPoint}/aladdin/api/v1/orders";

            $headers = [
                "Authorization" => "Bearer {$this->accessToken}",
                "Accept"        => "application/json",
                "Content-Type"  => "application/json"
            ];

            $body = [
                'store_id'            => $request->store_id,
                'merchant_order_id'   => $request->order_id,
                'sender_name'         => $request->sender_name,
                'sender_phone'        => $request->sender_phone,
                'recipient_name'      => $request->recipient_name,
                'recipient_phone'     => $request->recipient_phone,
                'recipient_address'   => $request->recipient_address,
                'recipient_city'      => $request->recipient_city_id,
                'recipient_zone'      => $request->recipient_zone_id,
                'recipient_area'      => $request->recipient_area_id,
                'delivery_type'       => $request->delivery_type,
                'item_type'           => $request->item_type,
                'special_instruction' => $request->special_instruction,
                'item_quantity'       => $request->item_quantity,
                'item_weight'         => $request->item_weight,
                'amount_to_collect'   => $request->collect_amount,
                'item_description'    => $request->item_description,
            ];

            $res = Http::withHeaders($headers)->post($url, $body);

            return json_decode($res, true);

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function createNewStore($request)
    {
        try {
            $url = "{$this->endPoint}/aladdin/api/v1/stores";

            $headers = [
                "Authorization" => "Bearer {$this->accessToken}",
                "Accept"        => "application/json",
                "Content-Type"  => "application/json"
            ];

            $body = [
                'name'              => $request->name,
                'contact_name'      => $request->contact_name,
                'contact_number'    => $request->contact_number,
                'secondary_contact' => $request->secondary_contact,
                'address'           => $request->address,
                'city_id'           => $request->city_id,
                'zone_id'           => $request->zone_id,
                'area_id'           => $request->area_id
            ];

            $res = Http::withHeaders($headers)->post($url, $body);

            return json_decode($res, true);

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function orderShipped($id)
    {
        try {
            $order = Order::withCount('items')->find($id);
            $user  = Auth::user();

            if (!$order) {
                throw new CustomException("Invalid order id");
            }

            $data = [
                'order'       => $order,
                'sender_info' => $user
            ];

            return $data;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function getStores()
    {
        try {
            $url = "{$this->endPoint}/aladdin/api/v1/stores";

            $headers = [
                "Authorization" => "Bearer {$this->accessToken}",
                "Accept"        => "application/json",
                "Content-Type"  => "application/json"
            ];

            $res = Http::withHeaders($headers)->get($url);

            return json_decode($res, true);

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function getCities()
    {
        try {
            $url = "{$this->endPoint}/aladdin/api/v1/countries/1/city-list";

            $headers = [
                "Authorization" => "Bearer {$this->accessToken}",
                "Accept"        => "application/json",
                "Content-Type"  => "application/json"
            ];

            $res = Http::withHeaders($headers)->get($url);

            return json_decode($res, true);

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function getZones($cityId)
    {
        try {
            $url = "{$this->endPoint}/aladdin/api/v1/cities/{$cityId}/zone-list";

            $headers = [
                "Authorization" => "Bearer {$this->accessToken}",
                "Accept"        => "application/json",
                "Content-Type"  => "application/json"
            ];

            $res = Http::withHeaders($headers)->get($url);

            return json_decode($res, true);

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function getAreas($zoneId)
    {
        try {
            $url = "{$this->endPoint}/aladdin/api/v1/zones/{$zoneId}/area-list";

            $headers = [
                "Authorization" => "Bearer {$this->accessToken}",
                "Accept"        => "application/json",
                "Content-Type"  => "application/json"
            ];

            $res = Http::withHeaders($headers)->get($url);

            return json_decode($res, true);

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function priceCalculation($request)
    {
        try {
            $url = "{$this->endPoint}/aladdin/api/v1/merchant/price-plan";

            $headers = [
                "Authorization" => "Bearer {$this->accessToken}",
                "Accept"        => "application/json",
                "Content-Type"  => "application/json"
            ];

            $body = [
                'store_id'       => $request->store_id,
                'item_type'      => $request->item_type,
                'delivery_type'  => $request->delivery_type,
                'item_weight'    => $request->item_weight,
                'recipient_city' => $request->recipient_city_id,
                'recipient_zone' => $request->recipient_zone_id
            ];

            $res = Http::withHeaders($headers)->post($url, $body);

            return json_decode($res, true);

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function updateEnvCredential($request)
    {
        try {
            $data = [
                'PATHAO_ENDPOINT'      => $request->pathao_endpoint,
                'PATHAO_CLIENT_ID'     => $request->pathao_client_id,
                'PATHAO_CLIENT_SECRET' => $request->pathao_client_secret,
                'PATHAO_USERNAME'      => $request->pathao_username,
                'PATHAO_PASSWORD'      => $request->pathao_password,
                'PATHAO_GRANT_TYPE'    => $request->pathao_grant_type,
            ];

            Helper::updateEnvVariable($data);

            $pathao = Pathao::firstOrNew();

            $pathao->endpoint      = $request->pathao_endpoint;
            $pathao->client_id     = $request->pathao_client_id;
            $pathao->client_secret = $request->pathao_endpoint;
            $pathao->username      = $request->pathao_endpoint;
            $pathao->password      = $request->pathao_endpoint;
            $pathao->grant_type    = $request->pathao_endpoint;
            $pathao->save();

            return $pathao;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function show()
    {
        try {
            return Pathao::with(["createdBy:id,username", "updatedBy:id,username"])->first();
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}

