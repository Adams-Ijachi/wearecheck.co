<?php

namespace App\Http\Controllers\Transactions;

use App\Actions\CreateTransactionAction;
use App\Exceptions\ApiCustomException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserTransactionRequest;
use App\Http\Resources\TransactionResource;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CreateUserTransactionController extends Controller
{

    /**
     * @throws ApiCustomException
     */
    public function __invoke(CreateTransactionAction $action, CreateUserTransactionRequest $request)
    {

        $user = Auth::user();

        $lock = Cache::lock('transaction '.$user->id, 10);

        try {
            $lock->block(5);
            $transaction = $action->execute($user,$request->validated());

            return TransactionResource::make($transaction)->additional([
                "message" => "Transaction successful"
            ]);

        } catch (LockTimeoutException $e) {
            throw new ApiCustomException('Please try again', 400);
        } finally {
            $lock->release();
        }

    }
}

