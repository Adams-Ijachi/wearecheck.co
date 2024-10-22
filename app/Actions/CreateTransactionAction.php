<?php

namespace App\Actions;

use App\Exceptions\ApiCustomException;
use App\Models\Transactions;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

class CreateTransactionAction
{
    public function __construct(private CreateUserWalletAction $createUserWalletAction)
    {
    }

    /**
     * @throws ApiCustomException
     */
    final function execute(User|Authenticatable $user, array $transactionData)
    {
        // if type is debit

        return DB::transaction(function () use ($user, $transactionData){

            $wallet = $user->wallet()->lockForUpdate()->first();
            $amount = $transactionData['amount'];

            if(!$wallet){
               $wallet =  $this->createUserWalletAction->execute(user: $user);
            }

            if ($transactionData['type'] == Transactions::CREDIT){

                $this->creditUserWallet(wallet: $wallet,amount: $amount);

                return $this->createTranctionRecord(user: $user, transactionData: $transactionData);
            }




            $this->confirmIfUserHasEnoughMoney(wallet: $wallet, amount: $amount);

            $this->debitUserWallet(wallet: $wallet,amount: $amount);

            return $this->createTranctionRecord(user: $user,transactionData: $transactionData);


        });

    }

    /**
     * @throws ApiCustomException
     */
    private function confirmIfUserHasEnoughMoney(Wallet $wallet, int $amount): void
    {
        if ($wallet->wallet_balance < $amount){
            throw new ApiCustomException("Insufficient Funds", 400);
        }
    }

    private function debitUserWallet(Wallet $wallet, int $amount)
    {
        $wallet->wallet_balance = $wallet->wallet_balance - $amount;

        $wallet->save();
    }

    private function createTranctionRecord(User $user , array $transactionData): Transactions
    {
        $transaction = new Transactions();

        $transaction->amount = $transactionData['amount'];
        $transaction->type = $transactionData['type'];
        $transaction->user_id = $user->id;

        $transaction->save();

        return $transaction;
    }

    private function creditUserWallet(Wallet $wallet, int $amount)
    {
        $wallet->wallet_balance = $wallet->wallet_balance + $amount;

        $wallet->save();
    }


}