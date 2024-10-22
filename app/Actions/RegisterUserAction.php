<?php

namespace App\Actions;



use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterUserAction
{

    final function execute(array $userData)
    {
        $user = new User();

        $user->name = $userData['name'];
        $user->email = $userData['email'];
        $user->password = Hash::make($userData['password']);

        $user->save();

        return $user;

    }
}