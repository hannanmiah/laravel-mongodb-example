<?php

use App\Models\User;

//beforeEach(function (){
//    $this->user = User::factory()->create();
//});
test('authenticated user can be retrieved', function () {
    $user = User::factory()->create();
    $res = $this->actingAs($user, 'api')->getJson(route('user.me'));
    $res->assertOk();
});
