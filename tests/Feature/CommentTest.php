<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->posts = Post::factory(5)->create();
    $this->post = $this->posts->random();
    $this->comments = Comment::factory(5)->create(['post_id' => $this->post->id, 'user_id' => $this->user->id]);
    $this->comment = $this->comments->random();

    // payload for post and put request
    $this->payload = [
        'content' => 'Lorem text for comment'
    ];
});

describe('index', function () {
    test('all comments from a post can be indexed', function () {
        // Send a GET request to fetch all comments from a post
        $res = $this->getJson(route('posts.posts.show.comments.index', $this->post->id));
        // Assert response status code for 200 OK
        $res->assertStatus(200);
        // Assert that all comments are returned
        $res->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'post_id',
                    'content',
                    'created_at',
                    'updated_at',
                ]
            ]
        ]);
    });

    test('it should return empty array when no comments are found', function () {
        $res = $this->getJson(route('posts.posts.show.comments.index', Post::factory()->create()->id));
        $res->assertStatus(200);
        $res->assertJson(['data' => []]);
    });
});

describe('store', function () {
    test('a new comment can be created', function () {
        // Send a POST request to create a new comment
        $res = $this->actingAs($this->user)->postJson(route('posts.posts.show.comments.store', $this->post->id), $this->payload);
        // Assert response status code for 201 Created
        $res->assertStatus(201);
        // Assert that the new comment is returned
        $res->assertJsonStructure([
            'id',
            'user_id',
            'post_id',
            'content',
            'created_at',
            'updated_at',
        ]);
    });

    test('un authenticated user can not post a comment', function () {
        // Send a POST request to create a new comment without authentication
        $res = $this->postJson(route('posts.posts.show.comments.store', $this->post->id), $this->payload);
        // Assert response status code for 401 Unauthorized
        $res->assertStatus(401);
    });
});

describe('show', function () {
    test('a specific comment can be fetched', function () {
        // Send a GET request to fetch a specific comment
        $res = $this->getJson(route('posts.posts.show.comments.show', [$this->post->id, $this->comment->id]));
        // Assert response status code for 200 OK
        $res->assertStatus(200);
        // Assert that the comment is returned
        $res->assertJsonStructure([
                'data' => [
                    'id',
                    'user_id',
                    'post_id',
                    'content',
                    'created_at',
                    'updated_at',
                ]
            ]
        );
    });
});

describe('update', function () {
    test('an existing comment can be updated', function () {
        // Send a PUT request to update an existing comment
        $res = $this->actingAs($this->user)->putJson(route('posts.posts.show.comments.update', [$this->post->id, $this->comment->id]), $this->payload);
        // Assert response status code for 200 OK
        $res->assertStatus(200);
        // Assert that the updated comment is returned
        $res->assertJsonStructure(
            ['id',
                'user_id',
                'post_id',
                'content',
                'created_at',
                'updated_at',
            ]
        );
    });

    test('unauthenticated user can not update a comment', function () {
        // Send a PUT request to update a comment without authentication
        $res = $this->putJson(route('posts.posts.show.comments.update', ['post' => $this->post, 'comment' => $this->comment]), $this->payload);
        // Assert response status code for 401 Unauthorized
        $res->assertStatus(401);
    });
});
