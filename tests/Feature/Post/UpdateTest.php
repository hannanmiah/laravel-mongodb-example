<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

beforeEach(function (){
    $this->user = User::factory()->create();
    $this->category = Category::factory()->create();

    $this->posts = Post::factory(5)->for($this->user)->create();
    $this->post = $this->posts->random();

    $this->payload = [
        'title' => 'Updated Post',
        'body' => 'This is updated post',
        'category_id' => $this->category->id,
    ];
});

test('user can update their own post', function () {
    $response = $this->actingAs($this->user,'sanctum')->putJson(route('posts.update', $this->post), $this->payload);
    $response->assertOk();
});

test('un authenticated user cannot update a post', function () {
    $response = $this->putJson(route('posts.update', $this->post), $this->payload);
    $response->assertUnauthorized();
});

test('unauthorized user can not update someone post',function (){
    $user = User::factory()->create();
    $response = $this->actingAs($user,'sanctum')->putJson(route('posts.update', $this->post), $this->payload);
    $response->assertForbidden();
});

test('Admin can update any post',function (){
    $user = User::factory()->create();
    $user->assignRole('Admin');
    $response = $this->actingAs($user,'sanctum')->putJson(route('posts.update', $this->post), $this->payload);
    $response->assertOk();
});

test('Editor can edit someone post',function (){
    $user = User::factory()->create();
    $user->assignRole('Editor');
    $response = $this->actingAs($user,'sanctum')->putJson(route('posts.update', $this->post), $this->payload);
    $response->assertOk();
});

test('User with other role can not edit someone post',function (){
    $user = User::factory()->create();
    $user->assignRole('Other role');
    $response = $this->actingAs($user,'sanctum')->putJson(route('posts.update', $this->post), $this->payload);
    $response->assertForbidden();
});
