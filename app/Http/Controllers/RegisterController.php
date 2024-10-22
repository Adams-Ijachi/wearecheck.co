<?php

namespace App\Http\Controllers;

use App\Actions\RegisterUserAction;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __invoke(RegisterUserAction $registerUserAction,RegisterUserRequest $request): \Illuminate\Http\JsonResponse
    {
        $registerUserAction->execute($request->validated());

        return response()->json([
            "message" =>"User registered successfully proceed to login"
        ]);

    }
}
