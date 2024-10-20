<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

beforeEach(function (){
    $this->user = User::factory()->create();
    $this->category = Category::factory()->create();
    $this->post = Post::factory()->create(['user_id' => $this->user->id,'category_id' => $this->category->id]);
});

test('user can delete his own post', function () {
    $response = $this->actingAs($this->user,'sanctum')->deleteJson(route('posts.destroy', $this->post));

    $response->assertStatus(204);

    // assert database missing the post
    $this->assertDatabaseMissing('posts', ['id' => $this->post->id]);
});

test('un authorized user can not delete someone post',function (){
    $user = User::factory()->create();
    $response = $this->actingAs($user,'sanctum')->deleteJson(route('posts.destroy', $this->post));

    $response->assertForbidden();
});

test('Admin can delete any post',function (){
    $user = User::factory()->create();
    $user->assignRole('Admin');
    $response = $this->actingAs($user,'sanctum')->deleteJson(route('posts.destroy', $this->post));

    $response->assertNoContent();
});

test('Editor can delete any post',function (){
    $user = User::factory()->create();
    $user->assignRole('Editor');
    $response = $this->actingAs($user,'sanctum')->deleteJson(route('posts.destroy', $this->post));

    $response->assertNoContent();
});