<?php

namespace App\Repositories;

use Exception;
use App\Models\Product;
use App\Models\Section;
use App\Classes\Helper;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class SectionRepository
{
    public function __construct(protected Section $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);
        $status       = $request->input("status", null);

        try {
            $sections = $this->model->with([
                "products",
                "products.category:id,name",
                "products.brand:id,name",
                "products.variations",
                "products.variations.attributeValue1:id,value",
                "products.variations.attributeValue2:id,value",
                "products.variations.attributeValue3:id,value",
                "createdBy:id,username"
            ])
            ->when($searchKey, fn($query) => $query->where("name", "like", "%$searchKey%"))
            ->when($status, fn ($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $sections;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $section = new $this->model();

            $section->title  = $request->title;
            $section->status = $request->status;
            $section->save();
            if ($request->category_id) {
                $productIds = Product::where("category_id", $request->category_id)->pluck("id");
            } else {
                $productIds = $request->product_ids;
            }

            $sectionProducts = [];
            if (count($productIds) > 0) {
                foreach ($productIds as $id) {
                    $sectionProducts[] = [
                        "section_id" => $section->id,
                        "product_id" => $id,
                        "created_at" => now()
                    ];
                }

                $section->sectionProducts()->insert($sectionProducts);
            }

            DB::commit();

            return $section;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $section = $this->model->with([
                "products",
                "products.category:id,name",
                "products.brand:id,name",
                "products.variations",
                "products.variations.attributeValue1:id,value",
                "products.variations.attributeValue2:id,value",
                "products.variations.attributeValue3:id,value",
                "createdBy:id,username",
                "updatedBy:id,username"
                ])->find($id);

            if (!$section) {
                throw new CustomException("Section not found");
            }

            return $section;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $section = $this->model->find($id);

            if (!$section) {
                throw new CustomException("Section Not found");
            }

            $section->title  = $request->title;
            $section->status = $request->status;
            $section->save();

            if ($request->category_id) {
                $productIds = Product::where("category_id", $request->category_id)->pluck("id");
            } else {
                $productIds = $request->product_ids;
            }

            // Delete section products
            $section->sectionProducts()->delete();

            $sectionProducts = [];
            if (count($productIds) > 0) {
                foreach ($productIds as $id) {
                    $sectionProducts[] = [
                        "section_id" => $section->id,
                        "product_id" => $id,
                        "created_at" => now()
                    ];
                }

                $section->sectionProducts()->insert($sectionProducts);
            }

            DB::commit();

            return $section;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $section = $this->model->find($id);
            if (!$section) {
                throw new CustomException("Section not found");
            }

            return $section->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);
        $status       = $request->input("status", null);

        try {
            $sections = $this->model->with([
                "products",
                "products.category:id,name",
                "products.brand:id,name",
                "products.variations",
                "products.variations.attributeValue1:id,value",
                "products.variations.attributeValue2:id,value",
                "products.variations.attributeValue3:id,value",
                "createdBy:id,username",
                "updatedBy:id,username"
            ])
                ->onlyTrashed()
                ->when($searchKey, fn($query) => $query->where("name", "like", "%$searchKey%"))
                ->when($status, fn($query) => $query->where("status", $status))
                ->orderBy('created_at', 'desc')
                ->paginate($paginateSize);

            return $sections;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $section = $this->model->onlyTrashed()->find($id);
            if (!$section) {
                throw new CustomException("Section not found");
            }

            $section->restore();

            return $section;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $section = $this->model->withTrashed()->find($id);
            if (!$section) {
                throw new CustomException("Section not found");
            }

            return $section->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
