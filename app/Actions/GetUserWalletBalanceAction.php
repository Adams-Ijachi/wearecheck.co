<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class GetUserWalletBalanceAction
{


    final function execute(User|Authenticatable $user)
    {
        return $user->wallet()->first();
    }
}