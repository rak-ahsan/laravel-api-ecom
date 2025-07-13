<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\AuthRepository;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Resources\Admin\UserResource;

class AuthController extends BaseController
{
    public function __construct(protected AuthRepository $repository){}

    function login(LoginRequest $request)
    {
        try {
            $user = $this->repository->login($request);

            return $this->sendResponse($user, "Login successfully done");

        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError('Something went wrong');
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
