<?php

namespace App\Http\Controllers\Front;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\BlogPostRepository;
use App\Http\Resources\Front\BlogPostResource;
use App\Http\Resources\Front\BlogPostCollection;

class BlogPostController extends BaseController
{
    protected $repository;

    public function __construct(BlogPostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE->value]);

            $blogPosts = $this->repository->index($request);

            $blogPosts = new BlogPostCollection($blogPosts);

                return $this->sendResponse($blogPosts, "Blog post list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE->value]);

            $blogPost = $this->repository->show($id);

            $blogPost = new BlogPostResource($blogPost);

            return $this->sendResponse($blogPost, "Blog post single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
