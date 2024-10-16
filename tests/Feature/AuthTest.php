<?php

use App\Models\User;

beforeEach(function (){
    $this->user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'password' => bcrypt('password'),
    ]);
});
test('authenticated user can be retrieved', function () {
    $user = User::factory()->create();
    $res = $this->actingAs($user, 'sanctum')->getJson(route('user.me'));
    $res->assertOk();
});

test('user can login', function () {
    $response = $this->postJson(route('auth.login'), [
        'email' => 'john.doe@example.com',
        'password' => 'password',
    ]);

    $response->assertOk();
    $response->assertJsonStructure(['access_token', 'token_type']);
});

test('user can register', function () {
    $response = $this->postJson(route('auth.register'), [
        'name' => 'Hannan Miah',
        'email' => 'hannan.maiah@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertCreated();
    $response->assertJsonStructure(['access_token', 'token_type']);
});

test('authenticated user can log out', function () {
    $response = $this->actingAs($this->user, 'sanctum')->postJson(route('auth.logout'));

    $response->assertNoContent();
});

test('unauthenticated user can not log out', function () {
    $response = $this->postJson(route('auth.logout'));

    $response->assertUnauthorized();
});
