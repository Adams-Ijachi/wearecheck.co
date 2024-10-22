<?php

namespace App\Actions;

use App\Exceptions\ApiCustomException;
use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserLoginAction
{

    /**
     * @throws \Exception
     */
    final function execute(array $userCredentials)
    {
        $user = User::query()
                ->where('email', $userCredentials['email'])
                ->first();

        if(!$user){
            throw new ApiCustomException("Invalid credentials",401);
        }

        if (! Hash::check($userCredentials['password'], $user->password)) {
            throw new ApiCustomException('Invalid credentials', 401);
        }

        return $user->createToken('auth-token')->plainTextToken;
    }

}