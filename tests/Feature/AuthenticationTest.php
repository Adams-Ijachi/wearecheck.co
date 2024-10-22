<?php

use App\Models\User;

beforeEach(function () {
    $this->seed(\Database\Seeders\UserSeeder::class);
});



test('A user can register', function () {

    $response = $this->post('/api/v1/auth/register', [
        'name' => "bruce wayne",
        'email' => "batman@gmail.com",
        'password' => 'password',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'message',
    ]);

    $user = User::query()->where('email', "batman@gmail.com")->exists();
    expect($user)->toBeTruthy();

});

test('A user can login', function () {

    $user = User::first();

    $response = $this->post('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'message',
        'access_token'
    ]);

});

test('A user gets an error for incorrect password', function () {


    $user = User::first();

    $response = $this->post('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'passwords',
    ]);

    $response->assertStatus(401);
    $response->assertJsonStructure([
        'message',
    ]);

});

test('A user gets an error for incorrect email', function () {


    $user = User::first();

    $response = $this->post('/api/v1/auth/login', [
        'email' => $user->email."s",
        'password' => 'password',
    ]);

    $response->assertStatus(401);
    $response->assertJsonStructure([
        'message',
    ]);

});