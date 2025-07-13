<?php

namespace App\Repositories;

use Exception;
use App\Models\Redx;
use App\Classes\Helper;
use Illuminate\Support\Facades\Http;

class RedxRepository
{
    protected $endpoint;
    protected $token;

    public function __construct()
    {
        $this->endpoint = config("redx.endpoint");
        $this->token    = config("redx.token");
    }

    public function getArea($request)
    {
        try {
            $postalCode   = $request->input('postal_code', null);
            $districtName = $request->input('district_name', null);
            $zoneId       = $request->input('zone_id', null);

            $endpoint = "$this->endpoint/v1.0.0-beta/areas?post_code=$postalCode&district_name=$districtName&zone_id=$zoneId";

            $headers = $this->getHeaders();

            $result  = Http::withHeaders($headers)->get($endpoint);

            return json_decode($result, true);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function createPickupStore($request)
    {
        try {
            $endpoint = "$this->endpoint/v1.0.0-beta/pickup/store";

            $headers = $this->getHeaders();

            $body = [
                "name"    => $request->name,
                "phone"   => $request->phone,
                "address" => $request->address,
                'area_id' => $request->areaId
            ];

            $result = Http::withHeaders($headers)->post($endpoint, $body);

            return json_decode($result, true);

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function getPickupStore()
    {
        try {
            $endpoint = "$this->endpoint/v1.0.0-beta/pickup/stores";

            $headers = $this->getHeaders();

            $result = Http::withHeaders($headers)->get($endpoint);

            return json_decode($result, true);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function getPickupStoreDetail($id)
    {
        try {
            $endpoint = "$this->endpoint/v1.0.0-beta/pickup/store/info/$id";

            $headers = $this->getHeaders();

            $result = Http::withHeaders($headers)->get($endpoint);

            return json_decode($result, true);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function parcelTrack($id)
    {
        try {
            $endpoint = "$this->endpoint/v1.0.0-beta/parcel/track/$id";

            $headers = $this->getHeaders();

            $result = Http::withHeaders($headers)->get($endpoint);

            return json_decode($result, true);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function parcelCreate($request)
    {
        try {
            $endpoint = "$this->endpoint/v1.0.0-beta/parcel";

            $headers = $this->getHeaders();

            $body = [
                "customer_name"          => $request->customer_name,
                "customer_phone"         => $request->customer_phone,
                "delivery_area"          => $request->delivery_area,
                "delivery_area_id"       => $request->delivery_area_id,
                "customer_address"       => $request->customer_address,
                "merchant_invoice_id"    => $request->merchant_invoice_id,
                "cash_collection_amount" => $request->cash_collection_amount,
                "parcel_weight"          => $request->parcel_weight,
                "instruction"            => $request->instruction,
                "value"                  => $request->value,
                "parcel_details_json"    => $request->parcel_details_json
            ];

            $result = Http::withHeaders($headers)->post($endpoint, $body);

            return json_decode($result, true);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function parcelDetail($id)
    {
        try {
            $endpoint = "$this->endpoint/v1.0.0-beta/parcel/info/$id";

            $headers = $this->getHeaders();

            $result = Http::withHeaders($headers)->get($endpoint);

            return json_decode($result, true);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function updateEnvCredential($request)
    {
        try {
            $data = [
                'REDX_ENDPOINT' => $request->redx_endpoint,
                'REDX_TOKEN'    => $request->redx_token
            ];

            Helper::updateEnvVariable($data);

            $redx = Redx::firstOrNew();

            $redx->endpoint = $request->redx_endpoint;
            $redx->token    = $request->redx_token;
            $redx->save();

            return $redx;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function show()
    {
        try {
            return Redx::with(["createdBy:id,username", "updatedBy:id,username"])->first();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    private function getHeaders()
    {
        return [
            "API-ACCESS-TOKEN" => "Bearer {$this->token}",
            "Accept"           => "application/json",
            "Content-Type"     => "application/json"
        ];
    }
}
