<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Models\BlogPost;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class BlogPostRepository
{
    public function __construct(protected BlogPost $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);
        $categoryId   = $request->input("category_id", null);

        try {

            $blogPosts = $this->model->with(["category:id,name", "tags:id,name", "createdBy:id,username"])
            ->orderBy('created_at', 'desc')
            ->when($searchKey, fn ($query) => $query->where("title", "like", "%$searchKey%"))
            ->when($categoryId, fn ($query) => $query->where("category_id", $categoryId))
            ->paginate($paginateSize);

            return $blogPosts;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $blogPost = new $this->model();

            $blogPost->title            = $request->title;
            $blogPost->category_id      = $request->category_id;
            $blogPost->meta_title       = $request->meta_title;
            $blogPost->meta_tag         = $request->meta_tag;
            $blogPost->description      = $request->description;
            $blogPost->meta_description = $request->meta_description;
            $blogPost->status           = $request->status;
            $blogPost->save();
            // Sync with tags
            $blogPost->tags()->sync($request->tag_ids);

            //update image
            if ($request->hasFile('image')) {
                Helper::uploadFile($blogPost, $request->image, $blogPost->uploadPath,  null , 1080, 720);
            }

            DB::commit();

            return $blogPost;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $blogPost = $this->model->with(["category:id,name", "tags:id,name", "createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$blogPost) {
                throw new CustomException("BlogPost not found");
            }

            return $blogPost;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $blogPost = $this->model->find($id);
            if (!$blogPost) {
                throw new CustomException("BlogPost Not found");
            }

            $blogPost->title            = $request->title;
            $blogPost->category_id      = $request->category_id;
            $blogPost->meta_title       = $request->meta_title;
            $blogPost->meta_tag         = $request->meta_tag;
            $blogPost->description      = $request->description;
            $blogPost->meta_description = $request->meta_description;
            $blogPost->status           = $request->status;
            $blogPost->save();

            // Sync with tags
            $blogPost->tags()->detach();
            $blogPost->tags()->sync($request->tag_ids);

            //update image
            if ($request->hasFile('image')) {
                Helper::uploadFile($blogPost, $request->image, $blogPost->uploadPath, $blogPost->img_path, 1080, 720);
            }

            DB::commit();

            return $blogPost;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $blogPost = $this->model->find($id);

            if (!$blogPost) {
                throw new CustomException("Blog post not found");
            }

            //  Delete old image
            if ($blogPost->img_path) {
                Helper::deleteFile ($blogPost->img_path);
            }

            return $blogPost->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);
        $categoryId   = $request->input("category_id", null);

        try {

            $blogPosts = $this->model->with(["category:id,name", "tags:id,name", "createdBy:id,username"])
            ->onlyTrashed()
            ->orderBy('created_at', 'desc')
            ->when($searchKey, fn($query) => $query->where("title", "like", "%$searchKey%"))
            ->when($categoryId, fn($query) => $query->where("category_id", $categoryId))
            ->paginate($paginateSize);

            return $blogPosts;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $blogPost = $this->model->onlyTrashed()->find($id);
            if (!$blogPost) {
                throw new CustomException("Blog post not found");
            }

            $blogPost->restore();

            return $blogPost;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $blogPost = $this->model->withTrashed()->find($id);
            if (!$blogPost) {
                throw new CustomException("Blog post not found");
            }

            return $blogPost->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
