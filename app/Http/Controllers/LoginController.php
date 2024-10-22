<?php

namespace App\Http\Controllers;

use App\Actions\UserLoginAction;
use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class LoginController extends Controller
{

    /**
     * @throws \Exception
     */
    public function __invoke(UserLoginAction $userLoginAction, LoginUserRequest $loginUserRequest): \Illuminate\Http\JsonResponse
    {

        $userToken = $userLoginAction->execute($loginUserRequest->validated());

        return response()->json([
            "message" => "User Logged In Successfully",
            "access_token" => $userToken
        ]);

    }
}
