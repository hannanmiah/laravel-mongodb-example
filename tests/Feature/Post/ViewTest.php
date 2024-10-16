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
        'data' => ['title', 'body', 'user_id', 'created_at', 'updated_at']
    ]);
});
