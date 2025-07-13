<?php

namespace App\Repositories;

use Exception;
use App\Models\User;
use App\Classes\Helper;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use App\Http\Resources\Admin\UserResource;
use Illuminate\Support\Facades\Hash;

class AuthRepository
{
    public function __construct(protected User $model){}

    public function register($request)
    {
        $verificationOtp = Helper::getVerificationOtp();

        try {
            DB::beginTransaction();

            $user = new $this->model();

            $user->username         = $request->username;
            $user->email            = $request->email;
            $user->phone_number     = $request->phone_number;
            $user->verification_otp = $verificationOtp;
            $user->type             = "customer";
            $user->password         = Hash::make($request->password);
            $user->save();

            DB::commit();

            return $user;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function login($request)
    {
        try {
            $user = $this->model->where('phone_number', $request->phone_number)->where("status", "active")->first();

            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    $token = $user->createToken('auth_token')->plainTextToken;

                    $user = new UserResource($user);

                    $data = [
                        'user'  => $user,
                        'token' => $token
                    ];

                    return $data;
                } else {
                    throw new CustomException("User credential dosen't match");
                }
            } else {
                throw new CustomException("User not found");
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function logout($request)
    {
        try {
            return $request->user()->tokens()->delete();
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
