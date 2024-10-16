<?php

use App\Models\Post;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->posts = Post::factory(5)->for($this->user)->create();

    Post::factory(10)->create();
});

test('it lists all posts', function () {
    $response = $this->getJson(route('posts.index'));

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'title', 'body', 'user_id', 'created_at', 'updated_at',
            ],
        ],
    ]);
});
