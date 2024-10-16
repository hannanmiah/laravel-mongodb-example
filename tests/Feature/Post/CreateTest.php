<?php


use App\Models\Category;
use App\Models\User;

beforeEach(function (){
    $this->user = User::factory()->create();
    $this->category = Category::factory()->create();

    $this->payload = [
        'title' => 'Test Post',
        'body' => 'This is a test post body. 1234567890',
        'category_id' => $this->category->id
    ];
});

test('it creates a new post', function () {
    // send request as authenticated user
    $res = $this->actingAs($this->user, 'sanctum')->postJson(route('posts.store'),$this->payload);
    // assert status code
    $res->assertCreated();
    // assert json fragment
    $res->assertJsonFragment($this->payload);
});

test('unauthenticated users cannot create posts', function () {
    // send request without authentication
    $res = $this->postJson(route('posts.store'), $this->payload);
    // assert status code
    $res->assertUnauthorized();
});

test('it throws validation errors for non-existent category', function () {
    $this->payload['category_id'] = 999999;
    // send request as authenticated user
    $res = $this->actingAs($this->user, 'sanctum')->postJson(route('posts.store'), $this->payload);
    // assert status code
    $res->assertUnprocessable();
    // assert validation error
    $res->assertJsonValidationErrors('category_id');
});