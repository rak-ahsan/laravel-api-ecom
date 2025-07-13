<?php

namespace App\Http\Controllers\Front;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\SubCategoryRepository;
use App\Http\Resources\Admin\SubCategoryResource;
use App\Http\Resources\Admin\SubCategoryCollection;

class SubCategoryController extends BaseController
{
    protected $repository;
    public function __construct(SubCategoryRepository $repository)
    {
        $this->repository = $repository;
    }
    public function index(Request $request)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE->value]);

            $subCategories = $this->repository->index($request);

            $subCategories = new SubCategoryCollection($subCategories);

            return $this->sendResponse($subCategories, "Sub Category list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE->value]);

            $subCategory = $this->repository->show($id);

            $subCategory = new SubCategoryResource($subCategory);

            return $this->sendResponse($subCategory, "Sub Category single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
