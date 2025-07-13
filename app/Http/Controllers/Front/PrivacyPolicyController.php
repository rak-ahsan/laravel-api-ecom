<?php

namespace App\Http\Controllers\Front;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\PrivacyPolicyRepository;
use App\Http\Resources\Front\PrivacyPolicyResource;
use App\Http\Resources\Front\PrivacyPolicyCollection;

class PrivacyPolicyController extends BaseController
{
    protected $repository;

    public function __construct(PrivacyPolicyRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE->value]);

            $privacies = $this->repository->index($request);

            $privacies = new PrivacyPolicyCollection($privacies);

            return $this->sendResponse($privacies, "Privacy Policy list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE->value]);

            $privacy = $this->repository->show($id);

            $privacy = new PrivacyPolicyResource($privacy);

            return $this->sendResponse($privacy, "Privacy Policy single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
