<?php

namespace App\Http\Controllers\Front;

use Exception;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use Illuminate\Support\Facades\Log;
use App\Repositories\ProductRepository;
use App\Http\Resources\Front\ProductResource;
use App\Http\Resources\Front\ProductCollection;
use App\Http\Requests\Front\ProductVariationRequest;

class ProductController extends BaseController
{
    protected $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $products = $this->repository->index($request);

            $products = new ProductCollection($products);

            return $this->sendResponse($products, "Product list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show($slug)
    {
        try {
            $status  = StatusEnum::ACTIVE->value;
            $product = $this->repository->show($slug, $status);

            $product = new ProductResource($product);

            return $this->sendResponse($product, "Product single data");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError("Something went wrong");
        }
    }

    public function categoryWiseProduct(Request $request, $slug)
    {
        try {
            $products = $this->repository->categoryWiseProduct($request, $slug);

            $products = new ProductCollection($products);

            return $this->sendResponse($products, "Category wise product", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError("Something went wrong");
        }
    }

    public function subCategoryWiseProduct(Request $request, $slug)
    {
        try {
            $products = $this->repository->subCategoryWiseProduct($request, $slug);

            $products = new ProductCollection($products);

            return $this->sendResponse($products, "Sub Category wise product", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError("Something went wrong");
        }
    }

    public function shopSideBar()
    {
        $maxPrice = Product::max("mrp");
        $minPrice = Product::min("mrp");

        $categories = Category::withCount("products")->where("status", "active")->having("products_count", ">", 0)->get();

        $brand = Brand::withCount("products")->where("status", "active")->having("products_count", ">", 0)->get();

        $products = [
            "max_price"  => $maxPrice,
            "min_price"  => $minPrice,
            "categories" => $categories,
            "brands"     => $brand,
        ];

        return $this->sendResponse($products, "Product price", 200);
    }

    function productVariation(ProductVariationRequest $request)
    {
        try {
            $variations = $this->repository->productVariation($request);

            return $variations;
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError("Something went wrong");
        }
        // $variationPrice = ProductVariation::select("product_id", "attribute_value_id_1", "attribute_value_id_2", "attribute_value_id_3", "mrp", "offer_price", "discount", "offer_percent")
        // ->where("product_id", $productId)
        // ->where("attribute_value_id_1", $attValue1)
        // ->where("attribute_value_id_2", $attValue2)
        // ->where("attribute_value_id_3", $attValue3)
        // ->first();

        // return $this->sendResponse($variationPrice, "Product price", 200);
    }
}
