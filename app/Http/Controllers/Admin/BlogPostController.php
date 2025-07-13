<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\BlogPostRepository;
use App\Http\Requests\Admin\BlogPostRequest;
use App\Http\Resources\Admin\BlogPostResource;
use App\Http\Resources\Admin\BlogPostCollection;


class BlogPostController extends BaseController
{
    public function __construct(protected BlogPostRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission("blog-posts-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $blogPosts = $this->repository->index($request);

            $blogPosts = new BlogPostCollection($blogPosts);

            return $this->sendResponse($blogPosts, "Blog post list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(BlogPostRequest $request)
    {
        if (!$request->user()->hasPermission("blog-posts-create")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $blogPost = $this->repository->store($request);

            $blogPost = new BlogPostResource($blogPost);

            return $this->sendResponse($blogPost, "Blog post created successfully", 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('blog-posts-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
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

    public function update(BlogPostRequest $request, $id)
    {
        if (!$request->user()->hasPermission("blog-posts-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $blogPost = $this->repository->update($request, $id);

            $blogPost = new BlogPostResource($blogPost);

            return $this->sendResponse($blogPost, "Blog post updated successfully", 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission("blog-posts-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $blogPost = $this->repository->delete($id);

            return $this->sendResponse($blogPost, "Blog post deleted successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission("blog-posts-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $blogPosts = $this->repository->trashList($request);

            $blogPosts = new BlogPostCollection($blogPosts);

            return $this->sendResponse($blogPosts, "Blog post trash list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('blog-posts-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $blogPost = $this->repository->restore($id);

            $blogPost = new BlogPostResource($blogPost);

            return $this->sendResponse($blogPost, "Blog post restore successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('blog-posts-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $blogPost = $this->repository->permanentDelete($id);

            return $this->sendResponse($blogPost, "Blog post Slider permanently deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
