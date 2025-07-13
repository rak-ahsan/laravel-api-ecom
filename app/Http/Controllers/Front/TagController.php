<?php

namespace App\Http\Controllers\Front;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use App\Repositories\TagRepository;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\Front\TagResource;
use App\Http\Resources\Front\TagCollection;

class TagController extends BaseController
{
    protected $repository;
    public function __construct(TagRepository $repository)
    {
        $this->repository = $repository;
    }
    public function index(Request $request)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE->value]);

            $tags = $this->repository->index($request);

            $tags = new TagCollection($tags);

            return $this->sendResponse($tags, "Tag list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE->value]);

            $tag = $this->repository->show($id);

            $tag = new TagResource($tag);

            return $this->sendResponse($tag, "Tag single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
