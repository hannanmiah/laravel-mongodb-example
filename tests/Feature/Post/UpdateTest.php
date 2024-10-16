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
