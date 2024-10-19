<?php

use App\Models\Category;
use App\Models\User;

beforeEach(function (){
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Admin');
    $this->user = User::factory()->create();
});
describe('create',function (){
    beforeEach(function (){
        $this->payload = [
            'name' => 'Test Category'
        ];
    });

    test('admin can create a category',function (){
        $response = $this->actingAs($this->admin,'sanctum')->postJson(route('admin.categories.store'), $this->payload);
        $response->assertCreated();
    });

    test('normal user can not create category',function (){
        $response = $this->actingAs($this->user,'sanctum')->postJson(route('admin.categories.store'), $this->payload);
        $response->assertForbidden();
    });

    test('unauthenticated user can not create category',function (){
        $response = $this->postJson(route('admin.categories.store'), $this->payload);
        $response->assertUnauthorized();
    });
});

describe('update',function (){
    beforeEach(function (){
        $this->payload = [
            'name' => 'Updated Category'
        ];
    });

    test('admin can update a category',function (){
        $category = Category::factory()->create();
        $response = $this->actingAs($this->admin,'sanctum')->putJson(route('admin.categories.update', $category), $this->payload);
        $response->assertOk();
    });

    test('normal user can not update a category',function (){
        $category = Category::factory()->create();
        $response = $this->actingAs($this->user,'sanctum')->putJson(route('admin.categories.update', $category), $this->payload);
        $response->assertForbidden();
    });

    test('unauthenticated user can not update a category',function () {
        $category = Category::factory()->create();
        $response = $this->putJson(route('admin.categories.update', $category), $this->payload);
        $response->assertUnauthorized();
    });
});

describe('delete',function (){
    test('admin can delete a category',function (){
        $category = Category::factory()->create();
        $response = $this->actingAs($this->admin,'sanctum')->deleteJson(route('admin.categories.destroy', $category));
        $response->assertNoContent();
    });

    test('normal user can not delete a category',function (){
        $category = Category::factory()->create();
        $response = $this->actingAs($this->user,'sanctum')->deleteJson(route('admin.categories.destroy', $category));
        $response->assertForbidden();
    });

    test('unauthenticated user can not delete a category',function () {
        $category = Category::factory()->create();
        $response = $this->deleteJson(route('admin.categories.destroy', $category));
        $response->assertUnauthorized();
    });
});

describe('index',function (){
    beforeEach(function (){
        $this->categories = Category::factory(20)->create();
    });
    test('admin can get all categories',function (){
        $categories = Category::factory(5)->create();
        $response = $this->actingAs($this->admin,'sanctum')->getJson(route('admin.categories.index'));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [ '*' => ['id', 'name', 'created_at', 'updated_at'] ]
        ]);
    });

    test('normal user can not get all categories',function (){
        $categories = Category::factory(5)->create();
        $response = $this->actingAs($this->user,'sanctum')->getJson(route('admin.categories.index'));
        $response->assertForbidden();
    });

    test('unauthenticated user can not get all categories',function () {
        $categories = Category::factory(5)->create();
        $response = $this->getJson(route('admin.categories.index'));
        $response->assertUnauthorized();
    });
});

describe('show',function (){
    test('admin can get a specific category',function (){
        $category = Category::factory()->create();
        $response = $this->actingAs($this->admin,'sanctum')->getJson(route('admin.categories.show', $category));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'created_at', 'updated_at']
        ]);
    });

    test('normal user can not get a specific category',function (){
        $category = Category::factory()->create();
        $response = $this->actingAs($this->user,'sanctum')->getJson(route('admin.categories.show', $category));
        $response->assertForbidden();
    });

    test('unauthenticated user can not get a specific category',function () {
        $category = Category::factory()->create();
        $response = $this->getJson(route('admin.categories.show', $category));
        $response->assertUnauthorized();
    });
});