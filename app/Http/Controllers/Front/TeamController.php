<?php

namespace App\Http\Controllers\Front;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\TeamRepository;
use App\Http\Resources\Front\TeamResource;
use App\Http\Resources\Front\TeamCollection;

class TeamController extends BaseController
{
    protected $repository;
    public function __construct(TeamRepository $repository)
    {
        $this->repository = $repository;
    }
    public function index(Request $request)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE->value]);

            $teams = $this->repository->index($request);

            $teams = new TeamCollection($teams);

            return $this->sendResponse($teams, "Team list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE->value]);

            $team = $this->repository->show($id);

            $team = new TeamResource($team);

            return $this->sendResponse($team, "Team single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
