<?php


use App\Models\Category;
use App\Models\Post;
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

test('tags can be attached to a post', function () {
    $tags = ['first-tag', 'second-tag'];
    $this->payload['tags'] = $tags;
    // send request as authenticated user
    $res = $this->actingAs($this->user, 'sanctum')->postJson(route('posts.store'), $this->payload);
    // assert status code
    $res->assertCreated();
    // assert database has the correct tags
    $this->assertDatabaseHas('tags', ['name' => $tags[0]]);
    // assert the post has the correct tags
    $post = Post::find($res->json('id'));
    // assert the post has the correct tags
    $this->assertCount(2, $post->tags);
});