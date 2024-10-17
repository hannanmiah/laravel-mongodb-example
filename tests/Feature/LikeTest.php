<?php

use App\Models\Like;
use App\Models\Post;
use App\Models\User;

beforeEach(function (){
    $this->user = User::factory()->create();
    // create some posts
    $this->posts = Post::factory(5)->for($this->user)->create();
});

describe('store',function (){
    test('it can store a new like for a post', function (){
        // Send a POST request to create a new like
        $res = $this->actingAs($this->user)->postJson(route('posts.posts.show.likes.store', $this->posts->random()->id));
        // Assert response status code for 201 Created
        $res->assertStatus(201);
        // Assert that the new like is returned
        $res->assertJsonStructure([
            'id',
            'user_id',
            'likeable_id',
            'likeable_type',
        ]);
    });

    test('un authenticated user cannot create a new like', function (){
        // Send a POST request to create a new like
        $res = $this->postJson(route('posts.posts.show.likes.store', $this->posts->random()->id));
        // Assert response status code for 401 Unauthorized
        $res->assertStatus(401);
    });
});

describe('destroy', function (){
    test('it can delete an existing like for a post', function (){
        $like = Like::factory()->for($this->user)->create();
        // Send a DELETE request to delete an existing like
        $res = $this->actingAs($this->user)->deleteJson(route('posts.posts.show.likes.destroy', [$this->posts->random()->id, $like->id]));
        // Assert response status code for 204 No Content
        $res->assertStatus(204);
    });

    test('unauthenticated user cannot delete an existing like', function (){
        $like = Like::factory()->for($this->user)->create();
        // Send a DELETE request to delete an existing like
        $res = $this->deleteJson(route('posts.posts.show.likes.destroy', [$this->posts->random()->id, $like->id]));
        // Assert response status code for 401 Unauthorized
        $res->assertStatus(401);
    });
});
