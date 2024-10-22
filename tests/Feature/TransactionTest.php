<?php

use App\Actions\CreateUserWalletAction;
use App\Models\User;
use Illuminate\Support\Facades\Cache;


beforeEach(function () {
    $this->seed(\Database\Seeders\UserSeeder::class);
});

test('A user can create a transaction credit', function () {


    $user = User::first();

    $response = $this->actingAs($user,'sanctum')->post('/api/v1/transactions', [
        'amount' => 1000,
        'type' => 'credit',
    ]);

    $response->assertStatus(201);

    expect($user->refresh()->transactions()->count())->toBe(1)
        ->and($user->refresh()->wallet->wallet_balance)->toBe(1000);

});
test('A user can create a transaction debit', function () {

    $user = User::first();
    app(CreateUserWalletAction::class)->execute($user);

    $wallet = $user->wallet()->first();

    $wallet->wallet_balance = 1000;
    $wallet->save();

    $response = $this->actingAs($user,'sanctum')->post('/api/v1/transactions', [
        'amount' => 1000,
        'type' => 'debit',
    ]);

    $response->assertStatus(201);

    expect($user->refresh()->transactions()->count())->toBe(1)
        ->and($user->refresh()->wallet->wallet_balance)->toBe(0);

});
test('An authenticated  user gets an error', function () {

    $user = User::first();
    app(CreateUserWalletAction::class)->execute($user);

    $wallet = $user->wallet()->first();

    $wallet->wallet_balance = 1000;
    $wallet->save();

    $response = $this->post('/api/v1/transactions', [
        'amount' => 1000,
        'type' => 'debit',
    ],[
        'Accept' => 'application/json',
    ]);

    $response->assertStatus(401);
});


test('A user can gets an Insufficient Fund Error during Debit', function () {

    $user = User::first();
    app(CreateUserWalletAction::class)->execute($user);

    $wallet = $user->wallet()->first();

    $wallet->wallet_balance = 1000;
    $wallet->save();

    $response = $this->actingAs($user,'sanctum')->post('/api/v1/transactions', [
        'amount' => 2000,
        'type' => 'debit',
    ]);

    $response->assertStatus(400);

    expect($user->refresh()->transactions()->count())->toBe(0)
        ->and($user->refresh()->wallet->wallet_balance)->toBe(1000);

});

test('Transaction Integrity is maintained during Concurrent requests  ', function () {
    $user = User::first();
    app(CreateUserWalletAction::class)->execute($user);

    $wallet = $user->wallet()->first();

    $wallet->wallet_balance = 2000;
    $wallet->save();

    $lock = Cache::lock('transaction '.$user->id, 30);
    $lock->acquire();

    $response = $this->actingAs($user,'sanctum')->post('/api/v1/transactions', [
        'amount' => 2000,
        'type' => 'debit',
    ]);

    $response->assertStatus(400);

});