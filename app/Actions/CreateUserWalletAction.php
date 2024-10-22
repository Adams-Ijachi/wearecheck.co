<?php

namespace App\Actions;

use App\Models\User;
use App\Models\Wallet;

class CreateUserWalletAction
{

    final function execute(User $user): Wallet
    {
        $wallet = new Wallet();
        $wallet->user_id = $user->id;
        $wallet->save();

        return $wallet;

    }

}