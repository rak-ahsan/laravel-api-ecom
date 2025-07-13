<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\TeamRepository;
use App\Http\Requests\Admin\TeamRequest;
use App\Http\Resources\Admin\TeamResource;
use App\Http\Resources\Admin\TeamCollection;

class TeamController extends BaseController
{
    public function __construct(protected TeamRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('teams-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $teams = $this->repository->index($request);

            $teams = new TeamCollection($teams);

            return $this->sendResponse($teams, "Team list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(TeamRequest $request)
    {
        if (!$request->user()->hasPermission("teams-create")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $team = $this->repository->store($request);

            $team = new TeamResource($team);

            return $this->sendResponse($team, "Team created successfully", 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('teams-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
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

    public function update(TeamRequest $request, $id)
    {
        if (!$request->user()->hasPermission('teams-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $team = $this->repository->update($request, $id);

            $team = new TeamResource($team);

            return $this->sendResponse($team, "Team updated successfully", 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function delete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('teams-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $team = $this->repository->delete($id);

            return $this->sendResponse($team, "Team deleted successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('teams-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $teams = $this->repository->trashList($request);

            $teams = new TeamCollection($teams);

            return $this->sendResponse($teams, "Team trash list", 200);
        }catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('teams-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $team = $this->repository->restore($id);

            $team = new TeamResource($team);

            return $this->sendResponse($team, "Team restore successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('teams-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $team = $this->repository->permanentDelete($id);

            return $this->sendResponse($team, "Team permanently deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
