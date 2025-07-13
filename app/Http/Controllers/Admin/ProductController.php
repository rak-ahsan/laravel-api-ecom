<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\ProductRepository;
use App\Http\Requests\Admin\ProductRequest;
use App\Http\Resources\Admin\ProductResource;
use App\Http\Resources\Admin\ProductCollection;
use App\Http\Requests\Admin\UpdateProductStatusRequest;

class ProductController extends BaseController
{
    public function __construct(protected ProductRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission("products-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $products = $this->repository->index($request);

            $products = new ProductCollection($products);

            return $this->sendResponse($products, "Product list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(ProductRequest $request)
    {
        if (!$request->user()->hasPermission("products-create")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $product = $this->repository->store($request);

            $product = new ProductResource($product);

            return $this->sendResponse($product, "Product created successfully", 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission("products-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $product = $this->repository->show($id);

            $product = new ProductResource($product);

            return $this->sendResponse($product, "Product single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(ProductRequest $request, $id)
    {
        if (!$request->user()->hasPermission("products-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {

            $product = $this->repository->update($request, $id);

            $product = new ProductResource($product);

            return $this->sendResponse($product, "Product updated successfully", 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission("products-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $product = $this->repository->destroy($id);

            return $this->sendResponse($product, "Product deleted successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function productHistory(Request $request, $id)
    {
        if (!$request->user()->hasPermission("products-history")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $audits = $this->repository->productHistory($request, $id);

            return $this->sendResponse($audits, "Product history", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function updateStatus(UpdateProductStatusRequest $request)
    {
        if (!$request->user()->hasPermission("products-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $product = $this->repository->updateStatus($request);

            return $this->sendResponse($product, "Product status updated successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission("products-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $products = $this->repository->trashList($request);

            $products = new ProductCollection($products);

            return $this->sendResponse($products, "Product trash list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission("products-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {

            $product = $this->repository->restore($id);

            $product = new ProductResource($product);

            return $this->sendResponse($product, "Product restore successfully", 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission("products-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {

            $product = $this->repository->permanentDelete($id);

            return $this->sendResponse($product, "Product permanent delete successfully", 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
