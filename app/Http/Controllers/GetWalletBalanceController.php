<?php

namespace App\Http\Controllers;

use App\Actions\GetUserWalletBalanceAction;
use App\Http\Resources\WalletResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetWalletBalanceController extends Controller
{
    public function __invoke(GetUserWalletBalanceAction $getUserWalletBalanceAction)
    {
        $user = Auth::user();

        $wallet = $getUserWalletBalanceAction->execute($user);

        return WalletResource::make($wallet)->additional([
            "message" => "User balance gotten successfully"
        ]);


    }
}
