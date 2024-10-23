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
                'title', 'body', 'user_id','published_at', 'created_at', 'updated_at',
            ],
        ],
    ]);
});

test('category,comments,user, likes can be include to the posts index',function (){
    $response = $this->getJson(route('posts.index', ['include' => 'category,comments,user,likes']));

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'title', 'body', 'user_id', 'category', 'comments', 'user', 'likes', 'published_at', 'created_at', 'updated_at',
            ],
        ],
    ]);
});

test('posts should missing unpublished post in response',function (){
    $unpublishedPost = Post::factory()->create(['published_at' => null]);

    $response = $this->getJson(route('posts.index'));

    $response->assertOk();
    $response->assertJsonMissing(['data' => ['id' => $unpublishedPost->id]]);
});

test('sort by -created_at',function (){
    $response = $this->getJson(route('posts.index', ['sort' => '-created_at']));

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            '*' => ['id', 'title', 'body', 'user_id', 'published_at', 'created_at', 'updated_at'],
        ],
    ]);
});
