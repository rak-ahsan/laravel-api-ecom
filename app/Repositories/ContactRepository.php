<?php

namespace App\Repositories;

use Exception;
use App\Models\Contact;
use App\Classes\Helper;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class ContactRepository
{
    public function __construct(protected Contact $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $brands = $this->model->with(["createdBy:id,username"])
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("name", "like", "%$searchKey%")
                ->orWhere("status", $searchKey);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $brands;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $contact = new $this->model();

            $contact->phone_number_1 = $request->phone_number_1;
            $contact->phone_number_2 = $request->phone_number_2;
            $contact->email          = $request->email;
            $contact->address        = $request->address;
            $contact->save();

            DB::commit();

            return $contact;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $contact = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);
            if (!$contact) {
                throw new CustomException("Contact not found");
            }
            return $contact;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $contact = $this->model->find($id);
            if (!$contact) {
                throw new CustomException("Contact Not found");
            }

            $contact->phone_number_1 = $request->phone_number_1;
            $contact->phone_number_2 = $request->phone_number_2;
            $contact->email          = $request->email;
            $contact->address        = $request->address;
            $contact->save();

            DB::commit();

            return $contact;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $contact = $this->model->find($id);
            if (!$contact) {
                throw new CustomException('Contact not found');
            }
            return $contact->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $brands = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("name", "like", "%$searchKey%")
                ->orWhere("status", $searchKey);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

            return $brands;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $contact = $this->model->onlyTrashed()->find($id);
            if (!$contact) {
                throw new CustomException('Contact not found');
            }

            $contact->restore();

            return $contact;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $contact = $this->model->withTrashed()->find($id);
            if (!$contact) {
                throw new CustomException('Contact not found');
            }

            return $contact->forceDelete();

        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
