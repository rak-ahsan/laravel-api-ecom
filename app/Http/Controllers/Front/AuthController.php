<?php

namespace App\Http\Controllers\Front;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use Illuminate\Support\Facades\Log;
use App\Repositories\AuthRepository;
use App\Http\Resources\Front\UserResource;
use App\Http\Requests\Front\RegisterRequest;

class AuthController extends BaseController
{
    protected $repository;

    public function __construct(AuthRepository $repository)
    {
        $this->repository = $repository;
    }

    function register(RegisterRequest $request)
    {
        try {
            $user = $this->repository->register($request);

            $user = new UserResource($user);

            return $this->sendResponse($user, 'Register successfully');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function login(Request $request)
    {

        try {
            $user = $this->repository->login($request);

            return $this->sendResponse($user, "Login successfully done");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function logout(Request $request)
    {
        try {
            $this->repository->logout($request);

            return $this->sendResponse(null, 'User logout successfully');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError('Something went wrong');
        }
    }
}
