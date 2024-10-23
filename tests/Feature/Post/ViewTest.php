<?php

use App\Models\Post;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->posts = Post::factory(5)->for($this->user)->create();
    $this->post = $this->posts->random();
});

test('get a specific post', function () {
    $response = $this->getJson(route('posts.show', $this->post));
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => ['title', 'body', 'user_id', 'published_at', 'created_at', 'updated_at']
    ]);
});

test('get a specific post with include', function () {
    $response = $this->getJson(route('posts.show', ['post' => $this->post, 'include' => 'category,comments,user,likes']));
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => ['title', 'body', 'user_id', 'category', 'comments', 'user', 'likes', 'published_at']
    ]);
});

test('unpublished post should return 404',function (){
    $unpublishedPost = Post::factory()->create(['published_at' => null]);

    $response = $this->getJson(route('posts.show', $unpublishedPost));
    $response->assertStatus(404);
});
