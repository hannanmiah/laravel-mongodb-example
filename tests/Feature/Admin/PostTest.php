<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

beforeEach(function (){
    $this->admin = User::factory()->create();
    $this->user = User::factory()->create();
    $this->admin->assignRole('Admin');
});

describe('index',function (){
    beforeEach(function (){
        $this->posts = Post::factory(3)->create();
    });

    test('admin can index all posts',function (){
        $response = $this->actingAs($this->admin,'sanctum')->getJson(route('admin.posts.index'));
        $response->assertOk();
        $response->assertJsonCount(3);
    });

    test('normal user can not index all posts',function (){
        $response = $this->actingAs($this->user,'sanctum')->getJson(route('admin.posts.index'));
        $response->assertForbidden();
    });

    test('unauthenticated user can not index all posts',function (){
        $response = $this->getJson(route('admin.posts.index'));
        $response->assertUnauthorized();
    });
});

describe('show',function (){
    beforeEach(function (){
        $this->posts = Post::factory(5)->create();
        $this->post = $this->posts->random();
    });

    test('admin can get a specific post',function (){
        $response = $this->actingAs($this->admin,'sanctum')->getJson(route('admin.posts.show', $this->post));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['title', 'body', 'user_id', 'created_at', 'updated_at']
        ]);
    });

    test('normal user can not get a post',function (){
        $response = $this->actingAs($this->user,'sanctum')->getJson(route('admin.posts.show', $this->post));
        $response->assertForbidden();
    });

    test('unauthenticated user can not show a post',function (){
        $response = $this->getJson(route('admin.posts.show', $this->post));
        $response->assertUnauthorized();
    });
});

describe('store',function (){
    beforeEach(function (){
        $this->payload = [
            'title' => 'New Post',
            'body' => 'This is a new post.',
            'category_id' => Category::factory()->create()->id
        ];
    });

    test('admin can create a new post',function (){
        $response = $this->actingAs($this->admin,'sanctum')->postJson(route('admin.posts.store'), $this->payload);
        $response->assertCreated();
    });

    test('normal user can not create a new post',function (){
        $response = $this->actingAs($this->user,'sanctum')->postJson(route('admin.posts.store'), $this->payload);
        $response->assertForbidden();
    });

    test('unauthenticated user can not create a new post',function (){
        $response = $this->postJson(route('admin.posts.store'), $this->payload);
        $response->assertUnauthorized();
    });

    test('it throws validation error for wrong category_id',function (){
        $this->payload['category_id'] = 999999;
        $response = $this->actingAs($this->admin,'sanctum')->postJson(route('admin.posts.store'), $this->payload);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('category_id');
    });
});
