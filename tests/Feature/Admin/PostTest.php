<?php

use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->user = User::factory()->create();
    $this->admin->assignRole('Admin');
});

describe('index', function () {
    beforeEach(function () {
        $this->posts = Post::factory(3)->has(Comment::factory(3))->has(Like::factory(3))->has(Tag::factory(5))->create();
    });

    test('admin can index all posts', function () {
        $response = $this->actingAs($this->admin, 'sanctum')->getJson(route('admin.posts.index'));
        $response->assertOk();
        $response->assertJsonCount(3);
    });

    test('normal user can not index all posts', function () {
        $response = $this->actingAs($this->user, 'sanctum')->getJson(route('admin.posts.index'));
        $response->assertForbidden();
    });

    test('unauthenticated user can not index all posts', function () {
        $response = $this->getJson(route('admin.posts.index'));
        $response->assertUnauthorized();
    });

    test('query can include category,comments,tags,likes', function () {
        $response = $this->actingAs($this->admin, 'sanctum')->getJson(route('admin.posts.index', ['include' => 'comments,tags,likes']));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['*' => ['id', 'title', 'body', 'user_id', 'created_at', 'updated_at', 'comments']]
        ]);
    });

    test('query can sort by created_at', function () {
        $response = $this->actingAs($this->admin, 'sanctum')->getJson(route('admin.posts.index', ['sort' => '-created_at']));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['*' => ['id', 'title', 'body', 'user_id', 'created_at', 'updated_at']]
        ]);
    });

    test('filter by category_id',function (){
        $category = Category::factory()->create();
        $posts = Post::factory(3)->for($category)->create();
        $response = $this->actingAs($this->admin, 'sanctum')->getJson(route('admin.posts.index', ['filter' => ['category_id' => $category->id]]));
        $response->assertOk();
        // assert count 3
        $response->assertJsonCount(3);
    });
});

describe('show', function () {
    beforeEach(function () {
        $this->posts = Post::factory(5)->has(Comment::factory(3))->create();
        $this->post = $this->posts->random();
    });

    test('admin can get a specific post', function () {
        $response = $this->actingAs($this->admin, 'sanctum')->getJson(route('admin.posts.show', $this->post));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['title', 'body', 'user_id', 'created_at', 'updated_at']
        ]);
    });

    test('normal user can not get a post', function () {
        $response = $this->actingAs($this->user, 'sanctum')->getJson(route('admin.posts.show', $this->post));
        $response->assertForbidden();
    });

    test('unauthenticated user can not show a post', function () {
        $response = $this->getJson(route('admin.posts.show', $this->post));
        $response->assertUnauthorized();
    });

    test('comments can be loaded with query',function (){
        $response = $this->actingAs($this->admin, 'sanctum')->getJson(route('admin.posts.show', $this->post). '?include=comments');
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['title', 'body', 'user_id', 'created_at', 'updated_at', 'comments' => ['*' => ['id', 'content', 'user_id', 'created_at', 'updated_at']]]
        ]);
    });
});

describe('store', function () {
    beforeEach(function () {
        $this->payload = [
            'title' => 'New Post',
            'body' => 'This is a new post.',
            'category_id' => Category::factory()->create()->id
        ];
    });

    test('admin can create a new post', function () {
        $response = $this->actingAs($this->admin, 'sanctum')->postJson(route('admin.posts.store'), $this->payload);
        $response->assertCreated();
    });

    test('normal user can not create a new post', function () {
        $response = $this->actingAs($this->user, 'sanctum')->postJson(route('admin.posts.store'), $this->payload);
        $response->assertForbidden();
    });

    test('unauthenticated user can not create a new post', function () {
        $response = $this->postJson(route('admin.posts.store'), $this->payload);
        $response->assertUnauthorized();
    });

    test('it throws validation error for wrong category_id', function () {
        $this->payload['category_id'] = 999999;
        $response = $this->actingAs($this->admin, 'sanctum')->postJson(route('admin.posts.store'), $this->payload);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('category_id');
    });
});
