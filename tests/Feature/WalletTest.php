<?php

use App\Actions\CreateUserWalletAction;
use App\Models\User;

beforeEach(function () {
    $this->seed(\Database\Seeders\UserSeeder::class);
});



test('A user gets wallet balance', function () {
    $user = User::first();
    app(CreateUserWalletAction::class)->execute($user);

    $wallet = $user->wallet()->first();

    $wallet->wallet_balance = 1000;
    $wallet->save();

    $response = $this->actingAs($user,'sanctum')->get('/api/v1/wallet/balance');

    $response->assertStatus(200);
    $response->assertJson([
        "data" => [
            "wallet_balance" => 1000
        ]
    ]);

});
