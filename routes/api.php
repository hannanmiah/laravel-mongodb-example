<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->get('/user', function (Request $request) {
    return $request->user();
})->name('user.me');

Route::prefix('posts')->name('posts.')->group(function () {
    Route::get('/', 'App\Http\Controllers\PostController@index')->name('index');
    Route::post('/', 'App\Http\Controllers\PostController@store')->name('store')->middleware('auth:api');
    Route::get('/{post}', 'App\Http\Controllers\PostController@show')->name('show');
    Route::put('/{post}', 'App\Http\Controllers\PostController@update')->name('update')->middleware('auth:api');
    Route::delete('/{post}', 'App\Http\Controllers\PostController@destroy')->name('destroy')->middleware('auth:api');
});