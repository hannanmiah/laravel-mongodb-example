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